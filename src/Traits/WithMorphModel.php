<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait WithMorphModel
{
    use WithModelResource {
        WithModelResource::allModels as callAllModels;
        WithModelResource::storeAction as callStoreAction;
    }

    /**
     * Parent morph Eloquent Model ::class string output.
     *
     * @var string
     */
    protected $morphModelClass;

    /**
     * Instance of parent morph Eloquent Model.
     *
     * @var Model|Builder
     */
    protected $morphModelInstance;

    /**
     * Property name used to access model instance from parent morph Eloquent Model.
     *
     * @var string
     */
    protected $morphType;

    /**
     * Register middleware on the controller.
     *
     * @param  array|string|\Closure $middleware
     * @param  array $options
     *
     * @return \Illuminate\Routing\ControllerMiddlewareOptions
     */
    abstract public function middleware($middleware, array $options = []);

    /**
     * Eloquent Model ::class.
     *
     * @return string
     */
    protected function modelClass()
    {
        return $this->morphModelClass();
    }

    /**
     * Eloquent Model instance.
     *
     * @return Builder|Model
     */
    protected function modelInstance()
    {
        return $this->morphModelInstance();
    }

    /**
     * Parent morph Eloquent Model ::class.
     *
     * @return string
     */
    protected function morphModelClass()
    {
        return $this->morphModelClass;
    }

    /**
     * Parent morph Eloquent Model instance.
     *
     * @return Builder|Model
     */
    protected function morphModelInstance()
    {
        if ($this->morphModelInstance instanceof $this->morphModelClass) {
            return $this->morphModelInstance;
        }

        return $this->morphModelInstance = new $this->morphModelClass();
    }

    /**
     * List of Model to check to make sure they exist.
     *
     * @return array
     */
    protected function modelList(): array
    {
        return [$this->morphModelClass, $this->modelClass];
    }

    /**
     * Connects Eloquent Model to parent morph Eloquent Model.
     *
     * @param Request $request
     * @return Model
     */
    protected function storeAction(Request $request): Model
    {
        $model = $this->callStoreAction($request);

        $attributes = array_merge($this->beforeStoreMorphModel($request), [
            "{$this->morphType()}_type" => $this->type,
            "{$this->morphType()}_id" => $model->id,
        ]);

        $this->setUserIdAttribute($attributes, __FUNCTION__);

        return $this->morphModelInstance->create($attributes);
    }

    /**
     * Method useful to add/update attributes for parent morph Eloquent Model.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function beforeStoreMorphModel(Request $request): array
    {
        return [];
    }

    /**
     * Saves parent morph Eloquent Model and updates connected Eloquent Model attributes.
     *
     * @param Request $request
     * @param Model $instance
     *
     * @return bool
     */
    protected function updateAction(Request $request, Model $instance): bool
    {
        $instance->save();

        $attributes = $this->getModelAttributes($request);
        $this->setUserIdAttribute($attributes, __FUNCTION__);

        return $instance->{$this->morphType()}->update($attributes) ?? false;
    }

    /**
     * Remove the the parent Eloquent Model and connected Eloquent Model from storage.
     *
     * @param  mixed $id
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy($id)
    {
        tap($this->findModel($id), function (Model $instance) {
            /** @var Model $model */
            $model = $instance->{$this->morphType()};
            $model->delete();
            $instance->delete();
        });

        return $this->finishAction(__FUNCTION__);
    }

    /**
     * Returns morph type property to be accessed when storing and updating.
     *
     * @return string
     */
    protected function morphType(): string
    {
        return $this->morphType ?? str_singular($this->morphModelInstance()->getTable());
    }

    /**
     * Adds morph type to query for getting all Eloquent Models from storage.
     *
     * @return Builder
     */
    protected function allModels(): Builder
    {
        return $this->callAllModels()->where("{$this->morphType()}_type", str_singular($this->type));
    }
}
