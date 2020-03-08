<?php

declare(strict_types=1);

namespace ResourceController\Controllers\JsonApi;

use ResourceController\Controllers\UserController as Controller;
use ResourceController\Traits\Controllers\PaginateIndex;
use ResourceController\Traits\Controllers\Response\WithJson;

class UserController extends Controller
{
    use PaginateIndex, WithJson;
}
