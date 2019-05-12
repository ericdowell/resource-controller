<?php

declare(strict_types=1);

namespace ResourceController\Traits\Http;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

trait AssignOwner
{
    /**
     * @param  string  $name
     * @param  \Illuminate\Http\Request|null  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    abstract protected function getModelParameter(string $name, Request $request = null): Model;

    /**
     * Get a new instance of the Model.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Eloquent|\Illuminate\Database\Eloquent\Model
     */
    abstract protected function newModel(): Model;

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    abstract protected function checkExistingResponse();

    /**
     * @return string
     */
    protected function getOwnerRouteParameter(): string
    {
        return 'user';
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return Model
     */
    protected function getOwnerModel(Request $request): Model
    {
        return $this->getModelParameter($this->getOwnerRouteParameter(), $request);
    }

    /**
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param  array  $attributes
     * @return void
     */
    protected function setForeignKeys(FormRequest $request, array &$attributes): void
    {
        $owner = $this->getOwnerModel($request);
        $attributes[$owner->getForeignKey()] = $owner->getKey();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function existingQuery(Request $request)
    {
        $owner = $this->getOwnerModel($request);

        return $this->newModel()->where($owner->getForeignKey(), '=', $owner->getKey());
    }

    /**
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    protected function checkExistingStore(FormRequest $request): ?JsonResponse
    {
        if (! $this->checkExisting || ! $this->existingQuery($request)->exists()) {
            return null;
        }

        return $this->checkExistingResponse();
    }
}