<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Mail\BookingApproved;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingRejected;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    // for authorize() with BookingPolicy
    use AuthorizesRequests;

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::all();
        $user = Auth::user();
        if ($user->isAdmin()) {
            // Admin sees all bookings
            $bookings = Booking::with(['user', 'room'])->orderBy('created_at', 'desc')->get();
        } else {
            // Regular user sees only their own bookings
            $bookings = Booking::where('user_id', $user->id)->with(['user', 'room'])->orderBy('created_at', 'desc')->get();
        }

        return view('bookings.index', compact('rooms', 'bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This method is not directly used in the multi-step modal flow.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        // dd($request->all());
        if (! $request->has('room_id') || ! $request->has('start_time') || ! $request->has('end_time')) {
            $error = 'Please select a room and specify the booking time.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $error], 422);
            }

            return back()->withErrors(['booking' => $error]);
        }

        $startTime = Carbon::parse($request->input('start_time'));
        $endTime = Carbon::parse($request->input('end_time'));

        // Server-side clash validation to prevent race conditions
        if ($this->bookingService->isClash($request->room_id, $startTime, $endTime)) {
            $error = 'The selected time slot is no longer available. Please choose another time.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $error], 422);
            }

            return back()->withErrors(['booking' => $error]);
        }

        $booking = Booking::create([
            'room_id' => $request->room_id,
            'user_id' => Auth::id(), // Assuming authenticated user
            'start_time' => $startTime,
            'end_time' => $endTime,
            'number_of_student' => $request->number_of_student,
            'equipment_needed' => $request->equipment_needed,
            'purpose' => $request->purpose,
            'status' => null, // pending
            'rejection_reason' => null,
        ]);

        // Send the email notification (with error handling)
        try {
            Mail::to(Auth::user()->email)->send(new BookingConfirmationMail($booking));
        } catch (\Exception $e) {
            // Log the error but don't crash the booking creation
            \Log::error('Failed to send booking confirmation email: '.$e->getMessage());
            // Optionally add a flash message
            if (! $request->expectsJson()) {
                session()->flash('warning', 'Booking created but email notification failed to send.');
            }
        }

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking' => $booking->load(['room', 'user']),
            ], 201);
        }

        return redirect()->route('bookings.my-bookings')->with('success', 'Booking created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        $rooms = Room::all();

        return view('bookings.edit', compact('booking', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $startTime = Carbon::parse($request->input('start_time'));
        $endTime = Carbon::parse($request->input('end_time'));

        if ($this->bookingService->isClash($request->room_id, $startTime, $endTime, $booking->id)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected time slot is no longer available. Please choose another time.',
                ], 422);
            }

            return back()->withErrors(['booking' => 'The selected time slot is no longer available. Please choose another time.'])->withInput();
        }

        $booking->update([
            'room_id' => $request->room_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'number_of_student' => $request->number_of_student,
            'equipment_needed' => $request->equipment_needed,
            'purpose' => $request->purpose,
            'status' => null, // Reset status to pending after edit
            'rejection_reason' => null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully!',
                'booking' => $booking->load(['room', 'user']),
            ]);
        }

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully!');
    }

    /**
     * Get bookings for a specific room.
     */
    public function getBookingsByRoom(Room $room)
    {
        $bookings = $room->bookings()->with(['user', 'room'])->orderBy('start_time')->get();

        return $bookings;
        // return response()->json($bookings);
    }

    /**
     * Get bookings for a specific room on a specific date.
     */
    public function getBookingsByRoomAndDate(Room $room, Request $request)
    {
        $query = $room->bookings();
        if ($request->has('date') && Carbon::parse($request->query('date'))->isValid()) {
            $selectedDate = Carbon::parse(time: $request->query('date'))->toDateString();
            $query->with(['user', 'room'])->whereDate('start_time', operator: $selectedDate);
        }

        $bookings = $query->with(['user', 'room'])->orderBy('start_time')->get();

        return $bookings;
    }

    public function approve(Booking $booking)
    {
        $this->authorize('approve', $booking);
        $booking->update(['status' => true]);

        Mail::to($booking->user->email)->queue(new BookingApproved($booking));

        return redirect()->route('bookings.index')->with('success', 'Booking approved successfully!');
    }

    public function reject(Booking $booking)
    {
        $this->authorize('reject', $booking);
        $booking->update([
            'status' => false,
            'rejection_reason' => request()->input('rejection_reason'),
        ]);

        Mail::to($booking->user->email)->queue(new BookingRejected($booking));

        return redirect()->route('bookings.index')->with('success', 'Booking rejected successfully!');
    }

    /**
     * Display user's booking history
     */
    public function myBookings()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->with(['user', 'room'])
            ->orderBy('start_time', 'desc')
            ->get();

        return view('bookings.my_bookings', compact('bookings'));
    }

    /**
     * Display pending bookings for admin approval
     */
    public function approvals()
    {
        $this->authorize('viewAny', Booking::class);

        $pendingBookings = Booking::whereNull('status')
            ->with(['user', 'room'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.approvals', compact('pendingBookings'));
    }

    /**
     * API: Get all bookings for calendar
     */
    public function apiIndex()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $bookings = Booking::with(['user', 'room'])->orderBy('start_time')->get();
        } else {
            $bookings = Booking::with(['user', 'room'])->orderBy('start_time')->get();
        }

        return response()->json($bookings);
    }

    /**
     * API: Get a single booking with relationships
     */
    public function apiShow(Booking $booking)
    {
        return response()->json($booking->load(['user', 'room']));
    }

    /**
     * API: Check room availability for a time slot
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $isAvailable = ! $this->bookingService->isClash(
            $request->room_id,
            Carbon::parse($request->start_time),
            Carbon::parse($request->end_time),
            $request->booking_id ?? null
        );

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable
                ? 'This time slot is available'
                : 'This time slot is already booked',
        ]);
    }

    /**
     * API: Get available rooms for a time slot
     */
    public function availableRooms(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        $startTime = Carbon::parse($request->start);
        $endTime = Carbon::parse($request->end);

        $allRooms = Room::all();
        $availableRooms = [];

        foreach ($allRooms as $room) {
            $isAvailable = ! $this->bookingService->isClash($room->id, $startTime, $endTime);
            if ($isAvailable) {
                $availableRooms[] = $room;
            }
        }

        return response()->json([
            'available_rooms' => $availableRooms,
            'count' => count($availableRooms),
        ]);
    }
}
