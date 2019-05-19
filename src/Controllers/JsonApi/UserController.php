<?php

declare(strict_types=1);

namespace ResourceController\Controllers\JsonApi;

use ResourceController\Traits\Controllers\PaginateIndex;
use ResourceController\Traits\Controllers\Response\WithJson;
use ResourceController\Controllers\UserController as Controller;

class UserController extends Controller
{
    use PaginateIndex, WithJson;
}
