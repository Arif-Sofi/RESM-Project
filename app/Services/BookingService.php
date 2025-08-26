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
    public function isClash(int $roomId, Carbon $newStartTime, Carbon $newEndTime): bool
    {
        $clashingEvents = Booking::where('room_id', $roomId)
            ->where(function ($query) use ($newStartTime, $newEndTime) {
                $query
                    ->whereBetween('start_time', [$newStartTime, $newEndTime])
                    ->orWhereBetween('end_time', [$newStartTime, $newEndTime])
                    ->orWhere(function ($query) use ($newStartTime, $newEndTime) {
                        $query->where('start_time', '<', $newStartTime)->where('end_time', '>', $newEndTime);
                    });
            })->exists();

        return $clashingEvents;
    }
}
