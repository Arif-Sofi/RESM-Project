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
        $rooms = Room::all();
        $user = Auth::user();
        if ($user->role->id === 1) {
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
        if (!$request->has('room_id') || !$request->has('start_time') || !$request->has('end_time')) {
            return back()->withErrors(['booking' => 'Please select a room and specify the booking time.']);
        }

        $startTime = Carbon::parse($request->input('start_time'));
        $endTime = Carbon::parse($request->input('end_time'));

        // Server-side clash validation to prevent race conditions
        if ($this->bookingService->isClash($request->room_id, $startTime, $endTime)) {
            return back()->withErrors(['booking' => 'The selected time slot is no longer available. Please choose another time.']);
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
        ]);

        // Send the email notification
        Mail::to(Auth::user()->email)->send(new BookingConfirmationMail($booking));

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

        Mail::to($booking->user->email)->send(new BookingRejectedMail($booking));
        return redirect()->route('bookings.index')->with('success', 'Booking rejected successfully!');
    }
}
