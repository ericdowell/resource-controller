<?php

declare(strict_types=1);

namespace ResourceController\Tests\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * ResourceController\Tests\Models\TestUser.
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property int $id
 * @property string $name
 * @property string|null $username
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestUser whereUpdatedAt($value)
 */
class TestUser extends Authenticatable
{
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
}
