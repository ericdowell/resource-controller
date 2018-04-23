<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits;

use Throwable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;

trait WithMorphModel
{
    use WithModelResource {
        WithModelResource::allModels as callAllModels;
        WithModelResource::storeAction as callStoreAction;
        WithModelResource::setModelInstance as callSetModelInstance;
    }

    /**
     * Instance of parent morph Eloquent Model.
     *
     * @var Model|Builder
     */
    protected $morphInstance;

    /**
     * Complete name/namespace of parent morph Eloquent Model.
     *
     * @var string
     */
    protected $morphModel;

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
    protected function modelClass()
    {
        return $this->morphModel;
    }

    /**
     * @return Builder|Model
     */
    protected function modelInstance()
    {
        return $this->morphInstance;
    }

    /**
     * @return array
     */
    protected function modelList(): array
    {
        return [$this->morphModel, $this->model];
    }

    /**
     * @param FormRequest $request
     * @return Model
     */
    protected function storeAction(FormRequest $request): Model
    {
        $morphType = $this->getMorphType();

        $model = $this->callStoreAction($request);

        $attributes = array_merge($this->beforeMorphStoreModel($request), [
            "{$morphType}_type" => $this->type,
            "{$morphType}_id" => $model->id,
        ]);

        $this->setUserIdAttribute($attributes, __FUNCTION__);

        return $this->morphInstance->create($attributes);
    }

    /**
     * @param FormRequest $request
     *
     * @return array
     */
    protected function beforeMorphStoreModel(FormRequest $request): array
    {
        return [];
    }

    /**
     * @param FormRequest $request
     * @param Model $instance
     *
     * @return bool
     */
    protected function updateAction(FormRequest $request, Model $instance): bool
    {
        $instance->save();

        $attributes = $this->getModelAttributes($request);
        $this->setUserIdAttribute($attributes, __FUNCTION__);

        return $instance->{$this->getMorphType()}->update($attributes) ?? false;
    }

    /**
     * Remove the specified resource from storage.
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
            $model = $instance->{$this->getMorphType()};
            $model->delete();
            $instance->delete();
        });

        return $this->finishAction(__FUNCTION__);
    }

    /**
     * @return string
     */
    protected function getMorphType(): string
    {
        return str_singular($this->morphInstance->getTable());
    }

    /**
     * @return Builder
     */
    protected function allModels(): Builder
    {
        return $this->callAllModels()->where("{$this->getMorphType()}_type", str_singular($this->type));
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    protected function setModelInstance(array &$context)
    {
        $this->morphInstance = $morphInstance = new $this->morphModel();

        $this->callSetModelInstance($context);

        return $this->mergeContext($context, compact('morphInstance'));
    }
}
