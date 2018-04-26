<?php

namespace EricDowell\ResourceController\Tests\Http\Controllers;

use EricDowell\ResourceController\Traits\WithModelResource;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Model;
use EricDowell\ResourceController\Tests\Models\TestUser;
use EricDowell\ResourceController\Traits\WithoutModelRequest;
use EricDowell\ResourceController\Http\Controllers\ResourceModelController;

class TestUserController extends ResourceModelController
{
    use WithoutModelRequest;
    use WithModelResource {
        WithModelResource::updateAction as callUpdateAction;
    }
    /**
     * Given a route action (key) set the form action (value).
     *
     * @var array
     */
    protected $actionMap = [
        'password-edit' => 'password-update',
    ];

    /**
     * @return bool
     */
    protected function withUser(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    protected function modelClass()
    {
        return TestUser::class;
    }

    /**
     * @param int $id
     *
     * @return Response
     * @throws Throwable
     */
    public function passwordEdit($id): Response
    {
        return $this->edit($id);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function passwordUpdate(Request $request, $id): RedirectResponse
    {
        $user = $this->findModel($id);
        if (! Hash::check($request->input('current_password'), $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password provided is incorrect.']);
        }
        $attributes = $request->all();
        $attributes['password'] = Hash::make($attributes['password']);

        $user->update($this->getModelAttributes($user, $attributes, true));

        return $this->finishAction(__FUNCTION__);
    }

    /**
     * Updates attributes based on request for Eloquent Model.
     *
     * @param Request $request
     * @param Model $instance
     *
     * @return bool
     */
    protected function updateAction(Request $request, Model $instance): bool
    {
        if ($request->isMethod('patch')) {
            return $this->upsertAction($request, $instance);
        }

        return $this->callUpdateAction($request, $instance);
    }
}
