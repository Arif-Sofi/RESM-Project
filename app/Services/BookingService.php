<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class BookingService
{
    /**
     * 指定された会議室、開始時間、終了時間で既存の予約と競合するかどうかを確認します。
     *
     * @return bool true if there is a clash, false otherwise.
     */
    public function isClash(int $roomId, Carbon $newStartTime, Carbon $newEndTime, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('room_id', $roomId);

        // Exclude current booking when updating
        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        // Two bookings clash if they overlap in time
        // They DON'T clash if: newEnd <= existingStart OR newStart >= existingEnd
        // So they DO clash if: NOT (newEnd <= existingStart OR newStart >= existingEnd)
        // Which simplifies to: newStart < existingEnd AND newEnd > existingStart
        $clashingEvents = $query->where('start_time', '<', $newEndTime)
            ->where('end_time', '>', $newStartTime)
            ->exists();

        return $clashingEvents;
    }
}
