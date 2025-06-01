<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Room;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingWizard extends Component
{
    public $currentStep = 1;
    public $selectedRoomId;
    public $selectedDate;
    public $selectedTime;
    public $previousBookings = [];
    public $numberOfStudents;
    public $equipmentNeeded;
    public $purpose;
    public $clashDetected = false;
    public $clashMessage = '';

    protected $rules = [
        'selectedDate' => 'required|date',
        'selectedTime' => 'required|date_format:H:i',
        'numberOfStudents' => 'required|integer|min:1',
        'purpose' => 'required|string|max:255',
        'equipmentNeeded' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        // 初期化時に必要なロジックがあればここに記述
    }

    #[On('open-booking-details-modal')]
    public function openBookingModal($roomId)
    {
        $this->selectedRoomId = $roomId;
        $this->currentStep = 1;
        $this->fetchBookings();
        $this->dispatch('open-modal', 'booking-details-modal');
    }

    public function fetchBookings()
    {
        if ($this->selectedRoomId) {
            $this->previousBookings = Booking::where('room_id', $this->selectedRoomId)
                                            ->orderBy('start_time')
                                            ->get();
        }
    }

    public function checkClash()
    {
        $this->resetValidation();
        $this->clashDetected = false;
        $this->clashMessage = '';

        $this->validate([
            'selectedDate' => 'required|date',
            'selectedTime' => 'required|date_format:H:i',
        ]);

        $start_time = $this->selectedDate . ' ' . $this->selectedTime . ':00';
        // Assuming 1-hour booking for now, adjust as needed
        $end_time = date('Y-m-d H:i:s', strtotime($start_time . ' +1 hour'));

        $clash = Booking::where('room_id', $this->selectedRoomId)
                        ->where(function ($query) use ($start_time, $end_time) {
                            $query->whereBetween('start_time', [$start_time, $end_time])
                                  ->orWhereBetween('end_time', [$start_time, $end_time])
                                  ->orWhere(function ($query) use ($start_time, $end_time) {
                                      $query->where('start_time', '<', $start_time)
                                            ->where('end_time', '>', $end_time);
                                  });
                        })
                        ->exists();

        if ($clash) {
            $this->clashDetected = true;
            $this->clashMessage = __('The selected time clashes with an existing booking.');
        } else {
            $this->currentStep = 2;
        }
    }

    public function goToStepOne()
    {
        $this->currentStep = 1;
        $this->resetValidation();
        $this->clashDetected = false;
        $this->clashMessage = '';
    }

    public function submitBooking()
    {
        $this->validate([
            'numberOfStudents' => 'required|integer|min:1',
            'purpose' => 'required|string|max:255',
            'equipmentNeeded' => 'nullable|string|max:500',
            'selectedDate' => 'required|date',
            'selectedTime' => 'required|date_format:H:i',
        ]);

        $start_time = $this->selectedDate . ' ' . $this->selectedTime . ':00';
        $end_time = date('Y-m-d H:i:s', strtotime($start_time . ' +1 hour')); // Assuming 1-hour booking

        // Re-check clash before saving to prevent race conditions
        $clash = Booking::where('room_id', $this->selectedRoomId)
                        ->where(function ($query) use ($start_time, $end_time) {
                            $query->whereBetween('start_time', [$start_time, $end_time])
                                  ->orWhereBetween('end_time', [$start_time, $end_time])
                                  ->orWhere(function ($query) use ($start_time, $end_time) {
                                      $query->where('start_time', '<', $start_time)
                                            ->where('end_time', '>', $end_time);
                                  });
                        })
                        ->exists();

        if ($clash) {
            $this->clashDetected = true;
            $this->clashMessage = __('The selected time clashes with an existing booking. Please select another time.');
            $this->currentStep = 1; // Go back to step 1 if clash detected
            return;
        }

        Booking::create([
            'room_id' => $this->selectedRoomId,
            'user_id' => \Auth::id(), // Assuming authenticated user
            'start_time' => $start_time,
            'end_time' => $end_time,
            'number_of_students' => $this->numberOfStudents,
            'equipment_needed' => $this->equipmentNeeded,
            'purpose' => $this->purpose,
        ]);

        session()->flash('message', __('Booking created successfully!'));
        $this->dispatch('close');
        $this->reset(); // Reset all properties after successful booking
        $this->currentStep = 1; // Ensure step is reset for next open
    }

    public function render()
    {
        return view('livewire.booking-wizard');
    }
}
