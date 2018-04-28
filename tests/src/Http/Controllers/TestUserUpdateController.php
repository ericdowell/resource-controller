<?php

namespace EricDowell\ResourceController\Tests\Http\Controllers;

use EricDowell\ResourceController\Tests\Models\TestUser;
use EricDowell\ResourceController\Traits\WithoutModelRequest;
use EricDowell\ResourceController\Http\Controllers\ResourceModelController;

class TestUserUpdateController extends ResourceModelController
{
    use WithoutModelRequest;

    /**
     * Given a route action (key) set the form action (value).
     *
     * @var array
     */
    protected $actionMap = [
        'password-edit' => 'password-update',
    ];

    /**
     * @var bool
     */
    protected $allowUpsert = false;

    /**
     * @var string
     */
    protected $editMethod = 'put';

    /**
     * @var string
     */
    protected $modelClass = TestUser::class;

    /**
     * @var bool
     */
    protected $withUser = false;
}
