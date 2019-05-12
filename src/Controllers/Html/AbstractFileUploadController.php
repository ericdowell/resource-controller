<?php

declare(strict_types=1);

namespace ResourceController\Controllers\Html;

use ResourceController\Traits\Http\AssignOwner;
use ResourceController\Traits\Http\UploadMediaFile;

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