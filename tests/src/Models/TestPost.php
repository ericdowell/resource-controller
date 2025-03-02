<?php

declare(strict_types=1);

namespace ResourceController\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ResourceController\Tests\Models\TestPost.
 *
 * @property-read \ResourceController\Tests\Models\TestUser $user
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property int $test_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestPost whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestPost whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestPost whereTestUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\ResourceController\Tests\Models\TestPost whereUpdatedAt($value)
 */
class TestPost extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
        'is_published',
        'test_user_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(TestUser::class);
    }
}
