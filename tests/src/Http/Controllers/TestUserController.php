<?php

namespace EricDowell\ResourceController\Tests\Http\Controllers;

use EricDowell\ResourceController\Tests\Models\TestUser;
use EricDowell\ResourceController\Traits\WithoutModelRequest;
use EricDowell\ResourceController\Http\Controllers\ResourceModelController;

class TestUserController extends ResourceModelController
{
    use WithoutModelRequest;

    /**
     * @var bool
     */
    protected $withUser = false;

    /**
     * Name of the affected Eloquent model.
     *
     * @var string
     */
    protected $modelClass = TestUser::class;
}
