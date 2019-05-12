<?php

declare(strict_types=1);

namespace ResourceController\Traits\Models;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

/**
 * ResourceController\Traits\Models\MediaModel
 *
 * @property string $path
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait MediaModel
{
    /**
     * @return string|
     */
    public static function disk(): string
    {
        /** @var string $disk */
        $disk = config('filesystems.media', 'public');

        return $disk;
    }

    /**
     * @return \Illuminate\Filesystem\FilesystemAdapter|\Illuminate\Contracts\Filesystem\Filesystem
     */
    public static function storage(): FilesystemAdapter
    {
        return Storage::disk(static::disk());
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return Str::singular($this->getTable());
    }

    /**
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    public function generatePath(UploadedFile $file): string
    {
        $basePath = $this->getBasePath();
        $hash = md5(vsprintf('%s|%s|%s|%s', [
            $this->disk(),
            $basePath,
            $file->getClientOriginalName(),
            now()->getTimestamp(),
        ]));

        return vsprintf('%s/%s/%s/%s', [
            $basePath,
            substr($hash, 0, 8),
            substr($hash, 8, 8),
            substr($hash, 16, 8),
        ]);
    }

    /**
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return $this->storage()->url($this->path);
    }

    /**
     * @return string
     */
    public function filenameDefaultPrefix()
    {
        $prefix = defined('static::FILENAME_DEFAULT_PREFIX') ? static::FILENAME_DEFAULT_PREFIX : 'user';

        return $this->display_type ?? ($prefix ? $prefix.'-' : ''.Str::slug($this->getBasePath()));
    }

    /**
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    public function getFilename(UploadedFile $file): string
    {
        if (! empty($file->getClientOriginalName()) && is_string($file->getClientOriginalName())) {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            return Str::slug($filename).'.'.$file->getClientOriginalExtension();
        }

        return $this->filenameDefaultPrefix().'-'.now()->getTimestamp().'.'.$file->getClientOriginalExtension();
    }

    /**
     * Save a new model and return the instance.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  array  $attributes
     * @return \ResourceController\Traits\Models\MediaModel|\Illuminate\Database\Eloquent\Model
     */
    public function createMedia(UploadedFile $file, array $attributes = []): Model
    {
        return tap($this->newModelInstance($attributes), function ($media) use ($file) {
            /** @var static $media */
            $media->fillMedia($file);
            $media->save();
        });
    }

    /**
     * Create and return an un-saved model instance, but file saved to disk.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  array  $attributes
     * @return \ResourceController\Traits\Models\MediaModel|\Illuminate\Database\Eloquent\Model
     */
    public function makeMedia(UploadedFile $file, array $attributes = []): Model
    {
        return tap($this->newModelInstance($attributes), function ($media) use ($file) {
            /** @var static|\Illuminate\Database\Eloquent\Model $media */
            $media->fillMedia($file);
        });
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return Model
     */
    public function fillMedia(UploadedFile $file): Model
    {
        $disk = $this->disk();
        $path = $this->generatePath($file);
        $filename = $this->getFilename($file);

        $storedPath = $file->storePubliclyAs($path, $filename, compact('disk'));

        return $this->fill([
            'filename' => $filename,
            'path' => $storedPath,
            'pathname' => str_replace($filename, '', $storedPath),
            'mime_type' => $this->storage()->mimeType($storedPath),
            'size' => $this->storage()->size($storedPath),
        ]);
    }

    /**
     * @return bool
     */
    public function deleteMedia(): bool
    {
        return $this->storage()->delete($this->path);
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function delete()
    {
        $deleted = parent::delete();
        if ($deleted) {
            return $this->deleteMedia();
        }

        return $deleted;
    }
}