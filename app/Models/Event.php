<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * このイベントを作成したユーザーを取得 (Userモデルとの1対多リレーション)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * このイベントに参加するスタッフを取得 (Userモデルとの多対多リレーション)
     */
    public function staff(): BelongsToMany
    {
        // 第2引数: ピボットテーブル名
        // 第3引数: このモデルの外部キー名 (event_id)
        // 第4引数: 関連モデルの外部キー名 (user_id)
        return $this->belongsToMany(User::class, 'event_staff_pivot', 'event_id', 'user_id');
    }
}
