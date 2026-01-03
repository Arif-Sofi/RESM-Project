<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;

    protected $endDate;

    protected $status;

    public function __construct($startDate, $endDate, $status)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Booking::with(['user', 'room']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('start_time', [$this->startDate, $this->endDate]);
        }

        if ($this->status !== null && $this->status !== 'all') {
            if ($this->status === 'pending') {
                $query->whereNull('status');
            } elseif ($this->status == '1') {
                $query->where('status', 1);
            } elseif ($this->status == '0') {
                $query->where('status', 0);
            }
        }

        return $query->orderBy('start_time', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Room',
            'User',
            'Purpose',
            'Start Time',
            'End Time',
            'Status',
        ];
    }

    public function map($booking): array
    {
        $status = 'Pending';
        if ($booking->status == 1) {
            $status = 'Approved';
        } elseif ($booking->status == 0 && $booking->status !== null) {
            $status = 'Rejected';
        }

        return [
            $booking->room->name,
            $booking->user->name,
            $booking->purpose,
            $booking->start_time->format('Y-m-d H:i:s'),
            $booking->end_time->format('Y-m-d H:i:s'),
            $status,
        ];
    }
}
