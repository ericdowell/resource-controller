<?php

declare(strict_types=1);

namespace ResourceController\Controllers\JsonApi;

use ResourceController\Traits\Http\AssignOwner;
use ResourceController\Traits\Http\UploadMediaFile;

abstract class AbstractFileUploadController extends AbstractModelController
{
    use UploadMediaFile, AssignOwner;

    /**
     * @var array
     */
    protected $skipAttributes = [
        'media',
    ];
}