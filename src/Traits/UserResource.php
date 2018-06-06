<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits;

use Illuminate\Console\Command;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use EricDowell\ResourceController\Exceptions\ModelClassCheckException;

trait UserResource
{
    /**
     * @param string|null $controller
     * @return void
     */
    public static function routes(string $controller = null)
    {
        static::passwordRoutes($controller);
        static::userResource($controller);
    }

    /**
     * @param string|null $controller
     * @return void
     */
    public static function passwordRoutes(string $controller = null)
    {
        $uri = 'user/password/{user}';
        $controller = $controller ?? 'UserController';

        Route::get("{$uri}/edit", "{$controller}@passwordEdit")->name('user.password-edit');
        Route::patch($uri, "{$controller}@passwordUpdate")->name('user.password-update');
    }

    /**
     * @param string|null $controller
     * @param array $options
     *
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public static function userResource(string $controller = null, array $options = [])
    {
        return Route::resource('user', $controller ?? 'UserController', $options);
    }

    /**
     * Get the User model instance.
     *
     * @param string|null $className
     * @return Model|\Illuminate\Database\Eloquent\Builder
     */
    protected function getUserInstance(string $className = null): Model
    {
        $userClassCheck = (new ModelClassCheckException())->setModel($this->getUserClassName($className));
        if (! $userClassCheck->classExists()) {
            throw $userClassCheck;
        }

        return $userClassCheck->getModelInstance();
    }

    /**
     * @param string|null $className
     *
     * @return string
     */
    protected function getUserClassName(string $className = null): string
    {
        if (is_string($className)) {
            return $className;
        }
        $fallback = rtrim(app()->getNamespace(), '\\').'\\User';
        $authUserModel = config('auth.providers.users.model', $fallback);

        if ($this instanceof Command) {
            $className = $this->option('model') ?? $authUserModel;
        } elseif ($this instanceof Controller) {
            $className = isset($this->modelClass) ? $this->modelClass : $authUserModel;
        }
        if (! $className || ! is_string($className)) {
            return $fallback;
        }

        return $className;
    }
}