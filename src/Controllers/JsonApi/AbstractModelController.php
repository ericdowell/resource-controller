<?php

declare(strict_types=1);

namespace ResourceController\Controllers\JsonApi;

use ResourceController\Traits\Controllers\Response\WithJson;
use ResourceController\Controllers\AbstractModelController as Controller;

abstract class AbstractModelController extends Controller
{
    use WithJson;

}
