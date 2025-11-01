<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class BookingService
{
    /**
     * 指定された会議室、開始時間、終了時間で既存の予約と競合するかどうかを確認します。
     *
     * @param int $roomId
     * @param Carbon $newStartTime
     * @param Carbon $newEndTime
     * @return bool true if there is a clash, false otherwise.
     */
    public function isClash(int $roomId, Carbon $newStartTime, Carbon $newEndTime, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('room_id', $roomId)
            ->whereIn('status', [null, 1]) // Only check pending or approved bookings
            ->where(function ($query) use ($newStartTime, $newEndTime) {
                $query
                    ->whereBetween('start_time', [$newStartTime, $newEndTime])
                    ->orWhereBetween('end_time', [$newStartTime, $newEndTime])
                    ->orWhere(function ($query) use ($newStartTime, $newEndTime) {
                        $query->where('start_time', '<=', $newStartTime)
                              ->where('end_time', '>=', $newEndTime);
                    });
            });

        // Exclude a specific booking (useful for updates)
        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }
}
