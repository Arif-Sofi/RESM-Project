<?php

namespace App\Http\Controllers;

use App\Exports\EventsExport;
use App\Imports\EventsImport;
use App\Mail\EventCreatedNotification;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class EventController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of events.
     */
    public function index()
    {
        $user = Auth::user();

        // Start building the query
        $query = Event::query();

        // If not admin, restrict to own events or where staff
        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('staff', function ($staffQuery) use ($user) {
                        $staffQuery->where('user_id', $user->id);
                    });
            });
        }

        // Get events
        $events = $query->with(['creator', 'staff'])
            ->orderBy('start_at', 'desc')
            ->get();

        // Get all users for staff selection
        $users = User::orderBy('name')->get();

        return view('events.index', compact('events', 'users'));
    }

    /**
     * Display user's own events.
     */
    public function myEvents()
    {
        $user = Auth::user();

        // Get only events where user is the creator
        $events = Event::where('user_id', $user->id)
            ->with(['creator', 'staff'])
            ->orderBy('start_at', 'desc')
            ->get();

        // Get all users for staff selection
        $users = User::orderBy('name')->get();

        return view('events.my_events', compact('events', 'users'));
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'staff' => 'nullable|array',
            'staff.*' => 'exists:users,id',
        ]);

        try {
            $event = DB::transaction(function () use ($validated, $request) {
                $event = Event::create([
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'location' => $validated['location'],
                    'start_at' => Carbon::parse($validated['start_at']),
                    'end_at' => isset($validated['end_at']) ? Carbon::parse($validated['end_at']) : null,
                    'user_id' => Auth::id(),
                ]);

                // Sync staff members
                if ($request->has('staff') && is_array($request->staff)) {
                    $event->staff()->sync($request->staff);
                }

                return $event;
            });

            // Send notifications
            $this->sendEventNotifications($event);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event created successfully!',
                    'event' => $event->load(['creator', 'staff']),
                ], 201);
            }

            return redirect()->route('events.index')->with('success', 'Event created successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to create event: '.$e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Failed to create event.'], 500);
            }

            return back()->withErrors(['event' => 'Failed to create event.'])->withInput();
        }
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'staff' => 'nullable|array',
            'staff.*' => 'exists:users,id',
        ]);

        try {
            DB::transaction(function () use ($event, $validated, $request) {
                $event->update([
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'location' => $validated['location'],
                    'start_at' => Carbon::parse($validated['start_at']),
                    'end_at' => isset($validated['end_at']) ? Carbon::parse($validated['end_at']) : null,
                ]);

                // Sync staff members
                $staffIds = $request->has('staff') && is_array($request->staff) ? $request->staff : [];
                $event->staff()->sync($staffIds);
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event updated successfully!',
                    'event' => $event->fresh()->load(['creator', 'staff']),
                ]);
            }

            return redirect()->route('events.index')->with('success', 'Event updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to update event: '.$e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Failed to update event.'], 500);
            }

            return back()->withErrors(['event' => 'Failed to update event.'])->withInput();
        }
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        try {
            $event->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event deleted successfully!',
                ]);
            }

            return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to delete event: '.$e->getMessage());

            if (request()->expectsJson()) {
                return response()->json(['message' => 'Failed to delete event.'], 500);
            }

            return back()->withErrors(['event' => 'Failed to delete event.']);
        }
    }

    /**
     * API: Get events for calendar (JSON).
     */
    public function apiEvents(Request $request)
    {
        $user = Auth::user();

        $query = Event::query();

        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('staff', function ($sq) use ($user) {
                        $sq->where('user_id', $user->id);
                    });
            });
        }

        // Filter by date range if provided
        if ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end);
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                    ->orWhereBetween('end_at', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_at', '<=', $start)
                            ->where('end_at', '>=', $end);
                    });
            });
        }

        $events = $query->with(['creator', 'staff'])->orderBy('start_at')->get();

        return response()->json($events);
    }

    /**
     * Send email notifications to creator and staff.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $import = new EventsImport;

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // This catches validation exceptions from the import class
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: {$failure->errors()[0]} on attribute {$failure->attribute()}";
            }

            return redirect()->route('events.index')
                ->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            // This catches other general exceptions during the import
            return redirect()->route('events.index')
                ->with('import_errors', ['An unexpected error occurred during the file import: '.$e->getMessage()]);
        }

        $importedCount = $import->getImportedCount();
        $errorMessages = $import->getErrorMessages();

        $statusMessage = "{$importedCount} events imported successfully.";

        if (count($errorMessages) > 0) {
            return redirect()->route('events.index')
                ->with('success', $statusMessage)
                ->with('import_errors', $errorMessages);
        }

        return redirect()->route('events.index')->with('success', $statusMessage);
    }

    /**
     * Export events to Excel.
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        return Excel::download(new EventsExport($startDate, $endDate, $status), 'events.xlsx');
    }

    private function sendEventNotifications(Event $event): void
    {
        $recipients = collect([$event->creator]);

        // Add staff members
        foreach ($event->staff as $staff) {
            if (! $recipients->contains('id', $staff->id)) {
                $recipients->push($staff);
            }
        }

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->queue(new EventCreatedNotification($event));
            } catch (\Exception $e) {
                \Log::error('Failed to send event notification to '.$recipient->email.': '.$e->getMessage());
            }
        }
    }
}
