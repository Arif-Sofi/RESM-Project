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

class BookingController extends Controller
{
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
        return view('bookings.index', compact('rooms'));
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
        if (!$request->has('room_id') || !$request->has('start_time') || !$request->has('end_time')) {
            return back()->withErrors(['booking' => 'Please select a room and specify the booking time.']);
        }

        $startTime = Carbon::parse($request->input('start_time'));
        $endTime = Carbon::parse($request->input('end_time'));

        // Server-side clash validation to prevent race conditions
        if ($this->bookingService->isClash($request->room_id, $startTime, $endTime)) {
            return back()->withErrors(['booking' => 'The selected time slot is no longer available. Please choose another time.']);
        }

        Booking::create([
            'room_id' => $request->room_id,
            'user_id' => Auth::id(), // Assuming authenticated user
            'start_time' => $startTime,
            'end_time' => $endTime,
            'number_of_student' => $request->number_of_student,
            'equipment_needed' => $request->equipment_needed,
            'purpose' => $request->purpose,
            'status' => null, // pending
        ]);

        return redirect()->route('dashboard')->with('success', 'Booking created successfully!');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }

    /**
     * Get bookings for a specific room.
     */
    public function getBookingsByRoom(Room $room)
    {
        $bookings = $room->bookings()->orderBy('start_time')->get();
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
            $query->whereDate('start_time', operator: $selectedDate);
        }

        $bookings = $query->orderBy('start_time')->get();

        return $bookings;
    }

    public function bookingApprove(Booking $booking)
    {
        $booking->status = 1;
        $booking->save();
        return redirect()->route('dashboard')->with('success', 'Booking approved successfully!');
    }
    public function bookingdisapprove(Booking $booking)
    {
        $booking->status = 0;
        $booking->save();
        return redirect()->route('dashboard')->with('success', 'Booking disapproved successfully!');
    }
}
