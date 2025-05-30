<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location_details',
    ];

    /**
     * 部屋に属する予約を取得するリレーション。
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
