<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Controllers;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use EricDowell\ResourceController\Traits\UserResource;
use EricDowell\ResourceController\Requests\UserRequest;

class UserController extends ResourceModelController
{
    use UserResource;

    /**
     * Given a route action (key) set the form action (value).
     *
     * @var array
     */
    protected $actionMap = [
        'password-edit' => 'password-update',
    ];

    /**
     * Route names of public actions, Auth Middleware are not applied to these.
     *
     * @var array
     */
    protected $publicActions = [];

    /**
     * @var array
     */
    protected $upsertExcept = [
        'password',
    ];

    /**
     * @var string
     */
    protected $userPrimaryKey = 'id';

    /**
     * A place to complete any addition constructor required logic.
     */
    protected function beginningConstruct(): void
    {
        if (empty($this->modelClass)) {
            $this->setModelClass(get_class($this->getUserInstance()));
        }
    }

    /**
     * @param int $id
     *
     * @return Response
     * @throws Throwable
     */
    public function passwordEdit($id)
    {
        return $this->edit($id);
    }

    /**
     * @param UserRequest $request
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function passwordUpdate(UserRequest $request, $id)
    {
        $user = $this->findModel($id);
        if (! Hash::check($request->input('current_password'), $user->password)) {
            $errors = ['current_password' => 'Current password provided is incorrect.'];

            return $this->redirectBack($errors, []);
        }
        $user->update(['password' => Hash::make($request->input('password'))]);

        return $this->finishAction(__FUNCTION__);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserRequest $request
     *
     * @return RedirectResponse
     */
    public function store(UserRequest $request)
    {
        return $this->storeModel($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserRequest $request
     * @param  mixed $id
     *
     * @return RedirectResponse
     */
    public function update(UserRequest $request, $id)
    {
        $user = $this->findModel($id);
        $currentPassword = $request->input('current_password');

        if (! Hash::check($currentPassword, $user->password)) {
            $inputs = request()->except('current_password');
            $errors = ['current_password' => 'Current password provided is incorrect.'];

            return $this->redirectBack($errors, $inputs);
        }

        return $this->updateModel($request, $id);
    }
}
