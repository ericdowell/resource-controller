<?php

declare(strict_types=1);

namespace ResourceController\Tests\Http\Controllers;

use ResourceController\Tests\Models\TestUser;
use ResourceController\Controllers\Html\UserController;
use ResourceController\Tests\Http\Requests\TestUser as TestUserRequest;

class TestUserController extends UserController
{
    /**
     * @var string
     */
    protected $model = TestUser::class;

    /**
     * @param  \ResourceController\Tests\Http\Requests\TestUser  $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(TestUserRequest $request)
    {
        return $this->modelStore($request);
    }

    /**
     * @param  \ResourceController\Tests\Http\Requests\TestUser  $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(TestUserRequest $request)
    {
        return $this->modelUpdate($request);
    }
}
