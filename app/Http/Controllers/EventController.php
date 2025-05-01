<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Mail\EventCreatedNotification;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $events = Event::where('user_id', $user->id)
            ->orWhereHas('staff', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with('creator', 'staff')
            ->get();

        $users = User::all();

        return view('events.index', compact('events', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $validatedData = $request->validated();
        $userTimezone = $request->input('user_timezone', config('app.timezone'));

        DB::beginTransaction();
        try {
            if (isset($validatedData['start_at'])) {
                $validatedData['start_at'] = Carbon::parse($validatedData['start_at'], $userTimezone)->utc();
            }
            if (isset($validatedData['end_at'])) {
                $validatedData['end_at'] = Carbon::parse($validatedData['end_at'], $userTimezone)->utc();
            } else {
                $validatedData['end_at'] = null;
            }

            // 認証ユーザーIDを追加
            $validatedData['user_id'] = Auth::id();

            // イベントを作成 (スタッフ情報を除くデータで一度作成)
            // staff 配列は create に含めない
            $eventDataToCreate = collect($validatedData)->except('staff')->toArray();
            $event = Event::create($eventDataToCreate);

            if (!empty($validatedData['staff'])) {
                $event->staff()->attach($validatedData['staff']);
            }
            DB::commit();

            // イベント作成者とスタッフに通知メールを送信
            if ($event->creator) {
                Mail::to($event->creator->email)->queue(new EventCreatedNotification($event));
            }

            // スタッフに通知メールを送信
            foreach ($event->staff as $staffMember) {
                Mail::to($staffMember->email)->queue(new EventCreatedNotification($event));
            }

            // 成功レスポンス
            return response()->json(
                [
                    'message' => 'イベントが作成されました。',
                    'event' => [
                        'id' => $event->id,
                        'title' => $event->title,
                        'start' => $event->start_at ? $event->start_at->toIso8601String() : null,
                        'end' => $event->end_at ? $event->end_at->toIso8601String() : null,
                        'description' => $event->description,
                        'creator' => $event->creator->name ?? 'N/A',
                        'staff' => $event->load('staff')->staff->pluck('name')->implode(', '),
                    ],
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack(); // エラー発生時はロールバック
            Log::error('Error creating event: ' . $e->getMessage());
            // エラーレスポンス
            return response()->json(['message' => 'イベントの作成中にエラーが発生しました。', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $validatedData = $request->validated();
        $userTimezone = $request->input('user_timezone', config('app.timezone'));

        DB::beginTransaction();
        try {
            if (isset($validatedData['start_at'])) {
                $validatedData['start_at'] = Carbon::parse($validatedData['start_at'], $userTimezone)->utc();
            }
            if (isset($validatedData['end_at'])) {
                $validatedData['end_at'] = Carbon::parse($validatedData['end_at'], $userTimezone)->utc();
            } else {
                $validatedData['end_at'] = null;
            }

            // イベントを更新
            $event->update(collect($validatedData)->except('staff')->toArray());

            // スタッフを更新
            if (isset($validatedData['staff'])) {
                $event->staff()->sync($validatedData['staff']);
            } else {
                $event->staff()->detach();
            }

            DB::commit();

            return response()->json(
                [
                    'message' => 'イベントが更新されました。',
                    'event' => [
                        'id' => $event->id,
                        'title' => $event->title,
                        'start' => $event->start_at ? $event->start_at->toIso8601String() : null,
                        'end' => $event->end_at ? $event->end_at->toIso8601String() : null,
                        'description' => $event->description,
                        'creator' => $event->creator->name ?? 'N/A',
                        'staff' => $event->load('staff')->staff->pluck('name')->implode(', '),
                        'staff_ids' => $event->staff->pluck('id')->toArray(),
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating event: ' . $e->getMessage());
            return response()->json(['message' => 'イベントの更新中にエラーが発生しました。', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }

    /**
     * APIエンドポイント: イベントの取得
     */
    public function apiEvents(Request $request)
    {
        $user = Auth::user();
        $query = Event::where('user_id', $user->id) // 自分が作成したイベント
            ->orWhereHas('staff', function ($query) use ($user) {
                // または、自分がスタッフとして参加しているイベント
                $query->where('users.id', $user->id);
            });

        // FullCalendarなどのために期間指定があれば適用
        if ($request->has(key: ['start', 'end'])) {
            $query->where(function ($q) use ($request) {
                $q->where('start_at', '<=', $request->input('end'))
                  ->where(function ($q2) use ($request) {
                      $q2->where('end_at', '>=', $request->input('start'))
                         ->orWhereNull('end_at');
                  });
            });
        }

        $events = $query
            ->with('creator:id,name', 'staff:id,name')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_at ? $event->start_at->toIso8601String() : null,
                    'end' => $event->end_at ? $event->end_at->toIso8601String() : null,
                    'description' => $event->description,
                    'creator' => $event->creator->name ?? 'N/A',
                    'staff' => $event->staff->pluck('name')->implode(', '),
                    'staff_ids' => $event->staff->pluck('id')->toArray(),
                    'creator_id' => $event->creator->id ?? null,
                ];
            });

        return response()->json($events);
    }
}
