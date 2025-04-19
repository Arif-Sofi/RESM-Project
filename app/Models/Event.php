<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;
    protected $table = 'events';
    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        // 'user_id',
    ];
}
