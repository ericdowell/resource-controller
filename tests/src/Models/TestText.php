<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * EricDowell\ResourceController\Tests\Models\TestText.
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $text
 * @property-read \EricDowell\ResourceController\Tests\Models\TestUser $user
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property int $id
 * @property string $text_type
 * @property int $text_id
 * @property int $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestText whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestText whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestText whereTextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestText whereTextType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestText whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\EricDowell\ResourceController\Tests\Models\TestText whereUserId($value)
 */
class TestText extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'texts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text_type',
        'text_id',
        'user_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function text()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(TestUser::class);
    }
}
