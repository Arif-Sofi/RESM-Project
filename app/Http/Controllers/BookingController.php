<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\BookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingApprovedMail;
use App\Mail\BookingRejectedMail;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class BookingController extends Controller
{
    //for authorize() with BookingPolicy
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
        $rooms = Room::select('id', 'name', 'capacity', 'building', 'floor', 'has_projector', 'has_whiteboard', 'equipment', 'description')
            ->orderBy('name')
            ->get();

        // Get bookings for the current week
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $bookings = Booking::with(['room:id,name', 'user:id,name'])
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->whereIn('status', [null, 1]) // pending or approved only
            ->orderBy('start_time')
            ->get();

        return Inertia::render('Bookings/Index', [
            'rooms' => $rooms,
            'bookings' => $bookings,
            'initialDateRange' => [
                'start' => $startOfWeek->toISOString(),
                'end' => $endOfWeek->toISOString(),
            ],
        ]);
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
        $startTime = Carbon::parse($request->input('start_time'));
        $endTime = Carbon::parse($request->input('end_time'));

        // Server-side clash validation to prevent race conditions
        if ($this->bookingService->isClash($request->room_id, $startTime, $endTime)) {
            return back()->withErrors([
                'start_time' => 'This time slot conflicts with an existing booking. Please choose another time.'
            ]);
        }

        $booking = Booking::create([
            'room_id' => $request->room_id,
            'user_id' => Auth::id(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'number_of_student' => $request->number_of_students ?? $request->number_of_student, // Support both field names
            'equipment_needed' => $request->equipment_needed,
            'purpose' => $request->purpose,
            'status' => null, // pending
            'rejection_reason' => null,
        ]);

        // Send the email notification
        Mail::to(Auth::user()->email)->send(new BookingConfirmationMail($booking));

        return back()->with('success', 'Booking created successfully! Awaiting approval.');
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
        $bookings = $room->bookings()->with('user')->orderBy('start_time')->get();
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
            $query->with('user')->whereDate('start_time', operator: $selectedDate);
        }

        $bookings = $query->orderBy('start_time')->get();

        return $bookings;
    }

    public function approve(Booking $booking)
    {
        // Authorize the 'update' action on the booking (in BookingPolicy).
        // Only the admin (user ID 1) will pass this check.
        $this->authorize('update', $booking);
        $booking->update(['status' => 1]);

        Mail::to($booking->user->email)->send(new BookingApprovedMail($booking));
        return redirect()->route('bookings.index')->with('success', 'Booking approved successfully!');
    }

    public function reject(Booking $booking)
    {
        $this->authorize('update', $booking);
        $booking->update(['status' => 0]);
        $booking->update(['rejection_reason' => request()->input('reason_reject')]);

        Mail::to($booking->user->email)->send(new BookingRejectedMail($booking));
        return redirect()->route('bookings.index')->with('success', 'Booking rejected successfully!');
    }
}
