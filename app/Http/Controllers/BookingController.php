<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
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
        $startTime = Carbon::parse($request->input('start_time'));
        $endTime = Carbon::parse($request->input('end_time')); // Assuming end_time is also passed or calculated

        // Server-side clash validation to prevent race conditions
        if ($this->isClash($request->room_id, $startTime, $endTime)) {
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

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully!');
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
        return response()->json($bookings);
    }

    /**
     * Check for booking clashes.
     */
    public function checkBookingClash(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $startTime = Carbon::parse($request->date . ' ' . $request->time);
        // Assuming a default booking duration, e.g., 1 hour
        $endTime = $startTime->copy()->addHour();

        $clash = $this->isClash($request->room_id, $startTime, $endTime);

        return response()->json([
            'clash' => $clash,
            'message' => $clash ? 'The selected time clashes with an existing booking.' : 'No clash detected.'
        ]);
    }

    /**
     * Helper function to check for booking clashes.
     */
    private function isClash($roomId, Carbon $startTime, Carbon $endTime)
    {
        return Booking::where('room_id', $roomId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime->subSecond()]) // Check if new booking starts within existing
                      ->orWhereBetween('end_time', [$startTime->addSecond(), $endTime]) // Check if new booking ends within existing
                      ->orWhere(function ($query) use ($startTime, $endTime) { // Check if new booking encompasses existing
                          $query->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                      });
            })
            ->exists();
    }
}
