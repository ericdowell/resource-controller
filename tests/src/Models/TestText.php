<?php declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Models;

use Illuminate\Database\Eloquent\Model;

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
