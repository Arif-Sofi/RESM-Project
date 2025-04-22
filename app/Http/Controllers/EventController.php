<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user) {
            //$tasks = Event::all(); 要らないかも
            return view('events.index', [
                //'tasks' => $tasks,
            ]);
        } else {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }
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

        // フロントエンドからユーザーのタイムゾーン名を受け取る（例: 'user_timezone'というパラメータ名）
        // もしタイムゾーン名がない場合は、Laravelのデフォルトタイムゾーン（config/app.php）やUTCなどを使用する
        $userTimezone = $request->get('user_timezone', config('app.timezone'));

        try {
            if (isset($validatedData['start_at'])) {
                $startAtInUserTimezone = Carbon::parse($validatedData['start_at'], $userTimezone);
                $validatedData['start_at'] = $startAtInUserTimezone->utc(); // UTCに変換
            }

            if (isset($validatedData['end_at'])) {
                $endAtInUserTimezone = Carbon::parse($validatedData['end_at'], $userTimezone);
                $validatedData['end_at'] = $endAtInUserTimezone->utc();
            } else {
                $validatedData['end_at'] = null;
            }
            $event = Event::create($validatedData);

            return response()->json(
                [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_at->toIso8601String(),
                    'end' => $event->end_at ? $event->end_at->toIso8601String() : null,
                ],
                201,
            );
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }

    /**
     * API endpoint to fetch events within a given date range.
     */
    public function apiEvents(Request $request)
    {
        $start = Carbon::parse($request->get('start'));
        $end = Carbon::parse($request->get('end'));

        $events = Event::where('start_at', '>=', $start)
            ->where('start_at', '<=', $end)
            ->orWhere(function ($query) use ($start, $end) {
                $query
                    ->where('start_at', '<=', $end) // 期間中に始まるイベント
                    ->where('end_at', '>=', $start); // 期間中に終わるイベント、または期間を完全に含むイベント
            })
            ->get();

        $formattedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_at->toIso8601String(),
                'end' => $event->end_at ? $event->end_at->toIso8601String() : null,
            ];
        });

        return response()->json($formattedEvents);
    }
}
