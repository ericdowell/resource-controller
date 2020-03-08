<?php

declare(strict_types=1);

namespace ResourceController\Traits\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use ResourceController\Contracts\Models\MediaModel;
use RuntimeException;

trait UploadMediaFile
{
    /**
     * Get a new instance of the Model.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Eloquent|\Illuminate\Database\Eloquent\Model
     */
    abstract protected function newModel(): Model;

    /**
     * Get the attributes from request for store action.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return array
     */
    abstract protected function getStoreAttributes(FormRequest $request): array;

    /**
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param  array  $attributes
     * @return void
     */
    abstract protected function setForeignKeys(FormRequest $request, array &$attributes): void;

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @throws \RuntimeException
     */
    protected function isModelInstanceOf(Model $model): void
    {
        if (! $model instanceof MediaModel) {
            $className = get_class($model);
            $message = "{$className} must extend ".MediaModel::class;
            throw new RuntimeException($message);
        }
    }

    /**
     * @param  \Illuminate\Foundation\Http\FormRequest|null  $request
     * @return \Illuminate\Http\UploadedFile
     */
    protected function getMedia(FormRequest $request): UploadedFile
    {
        return $request->file('media');
    }

    /**
     * Get attributes/foreign keys from request,
     * save a new model and return the instance.
     *
     * @param  FormRequest  $request
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \RuntimeException
     */
    protected function modelCreate(FormRequest $request): Model
    {
        /** @var MediaModel $media */
        $media = $this->newModel();

        $attributes = $this->getStoreAttributes($request);
        $this->setForeignKeys($request, $attributes);

        return $media->createMedia($this->getMedia($request), $attributes);
    }

    /**
     * Fill in request attributes and update model resource.
     *
     * @param  \Illuminate\Database\Eloquent\Model|MediaModel  $instance
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return bool
     */
    protected function fillAndUpdate(Model &$instance, FormRequest $request): bool
    {
        $instance->deleteMedia();

        return $instance->fillMedia($this->getMedia($request))->save();
    }
}
