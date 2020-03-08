<?php

declare(strict_types=1);

namespace ResourceController\Controllers\JsonApi;

use ResourceController\Controllers\AbstractModelController as Controller;
use ResourceController\Traits\Controllers\Response\WithJson;

abstract class AbstractModelController extends Controller
{
    use WithJson;
}
