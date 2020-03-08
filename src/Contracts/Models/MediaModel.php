<?php

declare(strict_types=1);

namespace ResourceController\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;

interface MediaModel
{
    /**
     * @return string
     */
    public static function disk(): string;

    /**
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public static function storage(): FilesystemAdapter;

    /**
     * @return string
     */
    public function getBasePath(): string;

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function generatePath(UploadedFile $file): string;

    /**
     * @return string
     */
    public function getUrlAttribute(): string;

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function getFilename(UploadedFile $file): string;

    /**
     * Save a new model and return the instance.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $attributes
     * @return \ResourceController\Traits\Models\MediaModel|\Illuminate\Database\Eloquent\Model
     */
    public function createMedia(UploadedFile $file, array $attributes = []): Model;

    /**
     * Create and return an un-saved model instance, but file saved to disk.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param  array $attributes
     * @return \ResourceController\Traits\Models\MediaModel|\Illuminate\Database\Eloquent\Model
     */
    public function makeMedia(UploadedFile $file, array $attributes = []): Model;

    /**
     * Fill the model with an array of attributes.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return Model
     */
    public function fillMedia(UploadedFile $file): Model;

    /**
     * @return bool
     */
    public function deleteMedia(): bool;
}
