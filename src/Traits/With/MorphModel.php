<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits\With;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;

trait MorphModel
{
    use MorphModelProps;
    use ModelResource {
        ModelResource::allModels as callAllModels;
        ModelResource::storeAction as callStoreAction;
    }

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
     * @return string
     */
    protected function findModelClass()
    {
        if (isset($this->findModelClass)) {
            return $this->findModelClass;
        }

        return $this->morphModelClass();
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
     * @return Builder|Eloquent
     */
    protected function morphModelInstance()
    {
        $morphModelClass = $this->morphModelClass();
        if ($this->morphModelInstance instanceof $morphModelClass) {
            return $this->morphModelInstance;
        }

        return $this->morphModelInstance = new $morphModelClass();
    }

    /**
     * List of Model to check to make sure they exist.
     *
     * @return array
     */
    protected function modelList(): array
    {
        return [$this->morphModelClass(), $this->modelClass()];
    }

    /**
     * Connects Eloquent Model to parent morph Eloquent Model.
     *
     * @param Request $request
     * @return Eloquent
     */
    protected function storeAction(Request $request): Eloquent
    {
        $model = $this->callStoreAction($request);

        $attributes = array_merge($this->beforeStoreMorphModel($request), [
            "{$this->morphType()}_id" => $model->id,
            "{$this->morphType()}_type" => $this->type,
        ]);

        $this->setUserIdAttribute($attributes, __FUNCTION__);

        return $this->morphModelInstance()->create($attributes);
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
     * @param Eloquent $instance
     *
     * @return bool
     */
    protected function updateAction(Request $request, Eloquent $instance): bool
    {
        $instance->save();

        $attributes = $this->getModelRequestAttributes($request, $instance->{$this->morphType()});
        $this->setUserIdAttribute($attributes, __FUNCTION__);

        return $instance->{$this->morphType()}->update($attributes) ?? false;
    }

    /**
     * Saves parent morph Eloquent Model and upsert attributes based on request for Eloquent Model.
     *
     * @param Request $request
     * @param Eloquent $instance
     *
     * @return bool
     */
    protected function upsertAction(Request $request, Eloquent $instance): bool
    {
        $instance->save();
        $attributes = $this->upsertAttributes($request, $instance->{$this->morphType()});
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
        tap($this->findModel($id), function (Eloquent $instance) {
            /** @var Eloquent $model */
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