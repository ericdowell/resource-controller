<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route as CurrentRoute;
use EricDowell\ResourceController\Traits\Model\WithProperties;
use EricDowell\ResourceController\Exceptions\ModelClassCheckException;

trait WithModel
{
    use WithProperties;

    /**
     * @return bool
     */
    protected function withUser(): bool
    {
        return $this->withUser;
    }

    /**
     * @return bool
     */
    protected function withoutUser(): bool
    {
        return ! $this->withUser();
    }

    /**
     * @return string
     */
    protected function findModelClass()
    {
        if (isset($this->findModelClass)) {
            return $this->findModelClass;
        }

        return $this->modelClass();
    }

    /**
     * @return Builder|Model
     */
    protected function findModelInstance()
    {
        $class = $this->findModelClass();

        return new $class();
    }

    /**
     * @return string
     */
    protected function modelNamespace()
    {
        if (isset($this->modelClassNamespace)) {
            return $this->modelClassNamespace;
        }

        return rtrim(app()->getNamespace(), '\\');
    }

    /**
     * @return string
     */
    protected function modelClass()
    {
        if (empty($this->modelClass)) {
            $modelClass = $this->modelNamespace().'\\';
            $modelClass .= str_replace(['\\', 'Controller'], '', studly_case(class_basename($this)));

            return $modelClass;
        }

        return $this->modelClass;
    }

    /**
     * @return Builder|Model
     */
    protected function modelInstance()
    {
        $modelClass = $this->modelClass();
        if ($this->modelInstance instanceof $modelClass) {
            return $this->modelInstance;
        }

        return $this->modelInstance = new $modelClass();
    }

    /**
     * @return null|int
     */
    protected function userId()
    {
        if (isset($this->userId)) {
            return $this->userId;
        }

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
        if ($this->withoutUser()) {
            return;
        }
        data_set($data, 'user_id', $this->userId());
    }

    /**
     * @return Builder
     */
    protected function allModels(): Builder
    {
        return $this->basicModelQuery();
    }

    /**
     * @return Builder
     */
    protected function basicModelQuery(): Builder
    {
        $query = $this->findModelInstance()->newQuery();

        $this->queryWith($query);

        if ($this->withoutUser()) {
            return $query;
        }

        return $this->queryWithUser($query);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    protected function queryWith(Builder &$query): Builder
    {
        return $query;
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    protected function queryWithUser(Builder &$query): Builder
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
        return $this->basicModelQuery()->findOrFail($id);
    }

    /**
     * @param Request $request
     * @param Model|null $instance
     *
     * @return array
     */
    protected function getModelRequestAttributes(Request $request, Model $instance = null): array
    {
        $data = $request->all();
        if ($request instanceof FormRequest) {
            $data = $request->validated();
        }

        return $this->getModelAttributes($instance ?? $this->modelInstance(), $data);
    }

    /**
     * @param Model $model
     * @param array $data
     * @param null|bool $modelFill
     *
     * @return array
     */
    protected function getModelAttributes(Model $model, array $data, bool $modelFill = null): array
    {
        $modelAttributes = [];

        foreach ($model->getFillable() as $key) {
            $modelAttributes[$key] = $this->getModelAttributeValue($model, $data, $key, $modelFill);
        }
        if (! $modelFill && isset($modelAttributes['password'])) {
            $modelAttributes['password'] = Hash::make($modelAttributes['password']);
        }

        return $modelAttributes;
    }

    /**
     * @param Model $model
     * @param array $data
     * @param $key
     * @param bool|null $modelFill
     *
     * @return bool|mixed
     */
    private function getModelAttributeValue(Model $model, array $data, $key, bool $modelFill = null)
    {
        $value = array_get($data, $key);
        if (is_null($value) && $model->exists && ! array_has($data, $key) && $modelFill) {
            return $model->getAttribute($key);
        }
        if (is_null($value) && $model->hasCast($key, 'boolean')) {
            return false;
        }

        return $value;
    }

    /**
     * @return array
     */
    protected function modelList(): array
    {
        return [$this->modelClass()];
    }

    /**
     * @return $this
     */
    protected function checkModels(): self
    {
        foreach ($this->modelList() as $model) {
            $this->checkModelExists($model);
        }

        return $this;
    }

    /**
     * @param string|null $model
     *
     * @return $this
     * @throws ModelClassCheckException
     */
    protected function checkModelExists(string $model = null): self
    {
        /** @var ModelClassCheckException $modelClassCheck */
        $modelClassCheck = with(new ModelClassCheckException())->setModel($model);
        if (! $modelClassCheck->classExists()) {
            throw $modelClassCheck;
        }

        return $this;
    }

    /**
     * @param \Illuminate\Routing\Route $route
     *
     * @return array
     */
    protected function generateDefaults(CurrentRoute $route = null): array
    {
        if (! $route || ! method_exists($route, 'getName') || empty($route->getName())) {
            return [];
        }
        $context = [];
        $template = $this->template = $this->template ?? $route->getName();

        $this->mergeContext($context, compact('template'))->setTypeAndFormAction($context)->setTypeName($context);
        $this->createModelInstance($context)->setMessageAndHeader($context);
        $this->setUserIdAttribute($context, __FUNCTION__);

        return $context;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setMessageAndHeader(array &$context): self
    {
        $btnMessage = sprintf('%s %s', ucfirst($this->formAction), ucfirst($this->type));
        $formHeader = ($this->formAction === 'update' ? ucfirst($this->formAction) : 'Create').' '.ucfirst($this->type);

        return $this->mergeContext($context, compact('btnMessage', 'formHeader'));
    }

    /**
     * Given a route action (key) set the form action (value).
     *
     * @return array
     */
    protected function actionMap(): array
    {
        if (isset($this->actionMap) && is_array($this->actionMap)) {
            return $this->actionMap;
        }

        return [];
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setTypeAndFormAction(array &$context): self
    {
        $actionMap = array_merge(['create' => 'store', 'edit' => 'update'], $this->actionMap());
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
    protected function setTypeName(array &$context): self
    {
        $this->typeName = $typeName = str_plural(ucfirst($this->type));

        return $this->mergeContext($context, compact('typeName'));
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function createModelInstance(array &$context): self
    {
        $instance = ${$this->type} = $this->findModelInstance();

        return $this->mergeContext($context, compact($this->type, 'instance'));
    }
}
