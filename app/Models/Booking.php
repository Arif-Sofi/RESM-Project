<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'start_time',
        'end_time',
        'status',

        'number_of_student',
        'equipment_needed',
        'purpose',
        'rejection_reason',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => 'boolean',
    ];

    /*
    changes timezone to Asia/Kuala_Lumpur when retrieving from database(UTC)
    */
    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->timezone('Asia/Kuala_Lumpur'),
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->timezone('Asia/Kuala_Lumpur'),
        );
    }

    /**
     * この予約を行ったユーザーを取得するリレーション。
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この予約が属する部屋を取得するリレーション。
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
