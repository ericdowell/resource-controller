<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits\With;

use Illuminate\Database\Eloquent\Builder;

trait AuthUser
{
    /**
     * @return Builder
     */
    protected function basicModelQuery(): Builder
    {
        if (isset($this->allowUserAccess)) {
            return $this->getModelQuery();
        }
        $userKey = 'user_id';
        if (isset($this->userKey)) {
            $userKey = $this->userKey;
        }

        return $this->getModelQuery()->where($userKey, '=', auth()->user()->getAuthIdentifier());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract protected function getModelQuery(): Builder;
}
