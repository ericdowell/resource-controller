<?php

namespace ResourceController\Tests\Http\Controllers;

use ResourceController\Tests\Models\TestUser;
use ResourceController\Controllers\UserController;

class TestUserController extends UserController
{
    /**
     * @var string
     */
    protected $model = TestUser::class;
}
