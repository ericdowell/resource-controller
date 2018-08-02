<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits\With;

use Illuminate\Database\Eloquent\Builder;

trait AuthUser
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    protected function queryWithUser(Builder &$query): Builder
    {
        return $query->with('user')->where('user_id', '=', auth()->user()->getAuthIdentifier());
    }
}
