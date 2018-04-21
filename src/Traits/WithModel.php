<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route as CurrentRoute;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait WithModel
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $typeName;

    /**
     * @var array
     */
    protected $mergeData = [];

    /**
     * Complete name/namespace of the Eloquent Model.
     *
     * @var string
     */
    protected $model;

    /**
     * Instance of the Eloquent Model.
     *
     * @var Model|Builder
     */
    protected $modelInstance;

    /**
     * @var bool
     */
    protected $withUser = true;

    /**
     * @var array
     */
    protected $actionMap = [
        'create' => 'store',
        'edit' => 'update',
    ];

    /**
     * @var array
     */
    protected $publicActions = [
        'index',
        'show',
    ];

    /**
     * @var string
     */
    protected $formAction;

    /**
     * @return string
     */
    protected function getFindModel()
    {
        return $this->model;
    }

    /**
     * @return bool
     */
    protected function withUser(): bool
    {
        return $this->withUser;
    }

    /**
     * @return Builder
     */
    protected function allModels(): Builder
    {
        $query = $this->modelInstance->newQuery();

        if (! $this->withUser()) {
            return $query;
        }

        return $query->with('user');
    }

    /**
     * @param mixed $id
     * @param callable|null $callback
     *
     * @return Model
     */
    protected function findModel($id, $callback = null): Model
    {
        if (! $this->withUser()) {
            return tap(forward_static_call([$this->getFindModel(), 'findOrFail'], $id), $callback);
        }

        return tap(forward_static_call([$this->getFindModel(), 'with'], 'user')->findOrFail($id), $callback);
    }

    /**
     * @param FormRequest $request
     *
     * @return array
     */
    protected function getModelAttributes(FormRequest $request): array
    {
        $modelAttributes = [];

        foreach ($this->modelInstance->getFillable() as $key) {
            $input = $request->input($key);
            if (is_null($input) && $this->modelInstance->hasCast($key, 'boolean')) {
                $input = false;
            }
            $modelAttributes[$key] = $input;
        }

        return $modelAttributes;
    }

    /**
     * @param string $model
     *
     * @return $this
     * @throws ModelNotFoundException
     */
    protected function checkModel(string $model)
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
    protected function setDefaults(CurrentRoute $route): array
    {
        $context = [];
        if (method_exists($route, 'getName') && ! empty($route->getName())) {
            $this->template = $this->template ?? $route->getName();

            $this->setTypeAndFormAction($context)->setTypeName($context)->setModelInstance($context);
        }

        return $context;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setTypeAndFormAction(array &$context)
    {
        // @todo: Add support for grouped/prefixed routes.
        list($type, $action) = explode('.', $this->template);

        if (array_key_exists($action, $this->actionMap)) {
            $action = $this->actionMap[$action];
        }
        $this->type = $type;
        $this->formAction = $action;

        $btnMessage = sprintf('%s %s', ucfirst($this->formAction), ucfirst($this->type));
        $formHeader = ($action === 'update' ? ucfirst($this->formAction) : 'Create').' '.ucfirst($this->type);

        $context = array_merge($context, compact('type', 'btnMessage', 'action', 'formHeader'));

        return $this;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setTypeName(array &$context)
    {
        $this->typeName = $typeName = str_plural(ucfirst($this->type));

        $context = array_merge($context, compact('typeName'));

        return $this;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setModelInstance(array &$context)
    {
        $this->modelInstance = $instance = new $this->model();

        $context = array_merge($context, compact('instance'));

        return $this;
    }
}
