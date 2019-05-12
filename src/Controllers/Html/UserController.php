<?php

declare(strict_types=1);

namespace ResourceController\Controllers\Html;

use Illuminate\Support\Facades\Hash;
use ResourceController\Traits\Http\PaginateIndex;

class UserController extends AbstractModelController
{
    use PaginateIndex;
}
