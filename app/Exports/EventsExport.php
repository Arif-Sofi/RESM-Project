<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventsExport implements FromCollection, WithHeadings, WithMapping
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
        $query = Event::query();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('start_at', [$this->startDate, $this->endDate]);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Title',
            'Description',
            'Status',
            'Start Date',
            'End Date',
        ];
    }

    public function map($event): array
    {
        return [
            $event->id,
            $event->title,
            $event->description,
            $event->status,
            $event->start_at->format('Y-m-d H:i:s'),
            $event->end_at->format('Y-m-d H:i:s'),
        ];
    }
}
