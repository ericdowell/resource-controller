<?php

declare(strict_types=1);

namespace ResourceController\Controllers\Html;

use ResourceController\Traits\Http\AssignOwner;

abstract class AbstractOwnerController extends AbstractModelController
{
    use AssignOwner;
}