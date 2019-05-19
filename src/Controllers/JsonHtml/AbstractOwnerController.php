<?php

declare(strict_types=1);

namespace ResourceController\Controllers\JsonHtml;

use ResourceController\Traits\Controllers\AssignOwner;

abstract class AbstractOwnerController extends AbstractModelController
{
    use AssignOwner;
}
