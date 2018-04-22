<?php

namespace EricDowell\ResourceController\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * EricDowell\ResourceController\Tests\Models\TestPost.
 *
 * @property-read \EricDowell\ResourceController\Tests\Models\TestUser $user
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property int $id
 * @property string $title
 * @property string $body
 * @property int $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestPost whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestPost whereUserId($value)
 */
class TestPost extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
        'user_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(TestUser::class);
    }
}
