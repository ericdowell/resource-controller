<?php

namespace EricDowell\ResourceController\Tests\Http\Controllers;

use EricDowell\ResourceController\Tests\Models\TestUser;
use EricDowell\ResourceController\Controllers\UserController;

class TestUserController extends UserController
{
    /**
     * @var string
     */
    protected $modelClass = TestUser::class;
    /**
     * Auth Middleware to apply to non-public routes.
     *
     * @var array
     */
    protected $authMiddleware = [];
}
