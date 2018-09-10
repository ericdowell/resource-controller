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
        if (isset($this->allowUserAccess) && $this->allowUserAccess === true) {
            return $this->getModelQuery();
        }
        $userPrimaryKey = 'user_id';
        if (isset($this->userPrimaryKey)) {
            $userPrimaryKey = $this->userPrimaryKey;
        }

        return $this->getModelQuery()->where($userPrimaryKey, '=', auth()->user()->getAuthIdentifier());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract protected function getModelQuery(): Builder;
}
