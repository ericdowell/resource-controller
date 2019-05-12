<?php

declare(strict_types=1);

namespace ResourceController\Traits\Http;

use RuntimeException;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use ResourceController\Contracts\Models\MediaModel;

trait UploadMediaFile
{
    /**
     * Get a new instance of the Model.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Eloquent|\Illuminate\Database\Eloquent\Model
     */
    abstract protected function newModel(): Model;

    /**
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