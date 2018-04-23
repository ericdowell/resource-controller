<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Route as CurrentRoute;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait WithModel
{
    /**
     * Current form action based on parsed route name.
     *
     * @var string
     */
    protected $formAction;

    /**
     * Eloquent Model ::class string output.
     *
     * @var string
     */
    protected $modelClass;

    /**
     * Instance of the Eloquent Model.
     *
     * @var Model|Builder
     */
    protected $modelInstance;

    /**
     * Matches the current route name.
     *
     * @var string
     */
    protected $template;

    /**
     * Model type based on parsed route name.
     *
     * @var string
     */
    protected $type;

    /**
     * Plural version of '$type' property, first letter is uppercase.
     *
     * @var string
     */
    protected $typeName;

    /**
     * Flag for setting/updating 'user_id' as attribute of Eloquent Model.
     *
     * @var bool
     */
    protected $withUser = true;

    /**
     * @return bool
     */
    protected function withUser(): bool
    {
        return $this->withUser;
    }

    /**
     * @return string
     */
    protected function modelClass()
    {
        return $this->modelClass;
    }

    /**
     * @return Builder|Model
     */
    protected function modelInstance()
    {
        return $this->modelInstance;
    }

    /**
     * @return null|int
     */
    protected function userId()
    {
        return request()->input('user_id') ?? data_get(auth()->user(), 'id');
    }

    /**
     * @param $data
     * @param string $method
     *
     * @return void
     */
    protected function setUserIdAttribute(&$data, $method): void
    {
        if (! $this->withUser()) {
            return;
        }
        data_set($data, 'user_id', $this->userId());
    }

    /**
     * @return Builder
     */
    protected function allModels(): Builder
    {
        $query = $this->modelInstance()->newQuery();

        if (! $this->withUser()) {
            return $query;
        }

        return $this->queryWithUser($query);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    protected function queryWithUser(Builder $query): Builder
    {
        return $query->with('user');
    }

    /**
     * @param mixed $id
     *
     * @return Model
     */
    protected function findModel($id): Model
    {
        if (! $this->withUser()) {
            return forward_static_call([$this->modelClass(), 'findOrFail'], $id);
        }

        return forward_static_call([$this->modelClass(), 'with'], 'user')->findOrFail($id);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getModelAttributes(Request $request): array
    {
        $modelAttributes = [];

        foreach ($this->modelInstance->getFillable() as $key) {
            $input = $request->input($key);
            if (is_null($input) && $this->modelInstance->hasCast($key, 'boolean')) {
                $input = false;
            }
            $modelAttributes[$key] = $input;
        }
        if (isset($modelAttributes['password'])) {
            $modelAttributes['password'] = Hash::make($modelAttributes['password']);
        }

        return $modelAttributes;
    }

    /**
     * @return array
     */
    protected function modelList(): array
    {
        return [$this->modelClass];
    }

    /**
     * @return $this
     */
    protected function checkModels()
    {
        foreach ($this->modelList() as $model) {
            $this->checkModelExists($model);
        }

        return $this;
    }

    /**
     * @param string $model
     *
     * @return $this
     * @throws ModelNotFoundException
     */
    protected function checkModelExists(string $model)
    {
        /** @var ModelNotFoundException $modelNotFound */
        $modelNotFound = with(new ModelNotFoundException())->setModel($model);
        if (! class_exists($modelNotFound->getModel())) {
            throw $modelNotFound;
        }

        return $this;
    }

    /**
     * @param \Illuminate\Routing\Route $route
     *
     * @return array
     */
    protected function generateDefaults(CurrentRoute $route): array
    {
        if (! method_exists($route, 'getName') || empty($route->getName())) {
            return [];
        }
        $context = [];
        $template = $this->template = $this->template ?? $route->getName();

        $this->mergeContext($context, compact('template'))->setTypeAndFormAction($context)->setTypeName($context);
        $this->setModelInstance($context)->setMessageAndHeader($context);
        $this->setUserIdAttribute($context, __FUNCTION__);

        return $context;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setMessageAndHeader(array &$context)
    {
        $btnMessage = sprintf('%s %s', ucfirst($this->formAction), ucfirst($this->type));
        $formHeader = ($this->formAction === 'update' ? ucfirst($this->formAction) : 'Create').' '.ucfirst($this->type);

        return $this->mergeContext($context, compact('btnMessage', 'formHeader'));
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setTypeAndFormAction(array &$context)
    {
        $actionMap = ['create' => 'store', 'edit' => 'update'];
        $nameParts = explode('.', $this->template);

        $action = array_pop($nameParts);
        $type = array_pop($nameParts);

        if (array_key_exists($action, $actionMap)) {
            $action = $actionMap[$action];
        }
        $this->type = $type;
        $this->formAction = $action;

        return $this->mergeContext($context, compact('type', 'action'));
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setTypeName(array &$context)
    {
        $this->typeName = $typeName = str_plural(ucfirst($this->type));

        return $this->mergeContext($context, compact('typeName'));
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setModelInstance(array &$context)
    {
        $this->modelInstance = $instance = new $this->modelClass();

        return $this->mergeContext($context, compact('instance'));
    }

    /**
     * @param $context
     * @param mixed ...$merge
     *
     * @return $this
     */
    protected function mergeContext(&$context, ...$merge)
    {
        array_unshift($merge, $context);

        $context = call_user_func_array('array_merge', $merge);

        return $this;
    }
}
