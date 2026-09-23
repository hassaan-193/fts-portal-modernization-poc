<?php

namespace App\Models;

use Eloquent as Model;

/**
 * One FCM registration token, i.e. one app install signed in as one user.
 *
 * Registering a token that already exists reassigns it rather than duplicating,
 * so handing a phone to a colleague does not leave the previous owner receiving
 * that device's notifications.
 */
class DeviceToken extends Model
{
    public $table = 'device_tokens';

    public $fillable = [
        'user_id',
        'token',
        'platform',
        'device_name',
        'last_used_at',
    ];

    protected $casts = [
        'id'      => 'integer',
        'user_id' => 'integer',
        'token'   => 'string',
    ];

    protected $dates = ['last_used_at'];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    /**
     * Claim a token for a user. Safe to call on every login.
     */
    public static function register($userId, $token, $platform = 'android', $deviceName = null)
    {
        return static::updateOrCreate(
            ['token' => $token],
            [
                'user_id'      => $userId,
                'platform'     => $platform,
                'device_name'  => $deviceName,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Every live token for a set of users.
     *
     * @param  array $userIds
     * @return array  plain token strings
     */
    public static function forUsers(array $userIds)
    {
        if (empty($userIds)) {
            return [];
        }

        return static::whereIn('user_id', $userIds)
            ->pluck('token')
            ->unique()
            ->values()
            ->all();
    }
}
