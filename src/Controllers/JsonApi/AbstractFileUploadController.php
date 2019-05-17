<?php

declare(strict_types=1);

namespace ResourceController\Controllers\JsonApi;

use ResourceController\Traits\Controllers\AssignOwner;
use ResourceController\Traits\Controllers\UploadMediaFile;

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
