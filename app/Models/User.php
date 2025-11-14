<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Mail\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * このユーザーが作成したイベントを取得 (Eventモデルとのリレーション)
     */
    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'user_id');
    }

    /**
     * Alias for createdEvents() for convenience
     */
    public function events(): HasMany
    {
        return $this->createdEvents();
    }

    /**
     * このユーザーが参加しているイベントを取得 (Eventモデルとの多対多リレーション)
     */
    public function participatingEvents(): BelongsToMany
    {
        // 第2引数: ピボットテーブル名
        // 第3引数: このモデルの外部キー名 (user_id)
        // 第4引数: 関連モデルの外部キー名 (event_id)
        return $this->belongsToMany(Event::class, 'event_staff_pivot', 'user_id', 'event_id');
    }

    /**
     * このユーザーが作成した部屋を取得 (Roomモデルとのリレーション)
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin() {
        return $this->role_id === 1;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
