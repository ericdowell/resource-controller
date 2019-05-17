<?php

declare(strict_types=1);

namespace ResourceController\Controllers\Html;

use ResourceController\Traits\Controllers\AssignOwner;
use ResourceController\Traits\Controllers\UploadMediaFile;

abstract class AbstractFileUploadController extends AbstractModelController
{
    use AssignOwner, UploadMediaFile;

    /**
     * @var array
     */
    protected $skipAttributes = [
        'media',
    ];
}
