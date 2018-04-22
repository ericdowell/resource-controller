<?php

namespace EricDowell\ResourceController\Tests\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use EricDowell\ResourceController\Tests\Models\TestUser;
use EricDowell\ResourceController\Tests\Http\Requests\TestUserRequest;
use EricDowell\ResourceController\Http\Controllers\ResourceModelController;

class TestUserController extends ResourceModelController
{
    /**
     * @var bool
     */
    protected $withUser = false;

    /**
     * Name of the affected Eloquent model.
     *
     * @var string
     */
    protected $model = TestUser::class;

    /**
     * Store a newly created resource in storage.
     *
     * @param  TestUserRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TestUserRequest $request): RedirectResponse
    {
        return $this->storeModel($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TestUserRequest $request
     * @param  mixed $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TestUserRequest $request, $id): RedirectResponse
    {
        return $this->updateModel($request, $id);
    }
}
