<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'notification_type',
        'email',
        'in_app',
        'sms',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email' => 'boolean',
        'in_app' => 'boolean',
        'sms' => 'boolean',
    ];

    /**
     * Get the user that owns the notification preference.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a user's preference for a specific notification type.
     *
     * @param int $userId
     * @param string $notificationType
     * @return self
     */
    public static function getPreference(int $userId, string $notificationType): self
    {
        return self::firstOrCreate(
            [
                'user_id' => $userId,
                'notification_type' => $notificationType,
            ],
            [
                'email' => true,
                'in_app' => true,
                'sms' => false,
            ]
        );
    }
}
