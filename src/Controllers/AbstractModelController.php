<?php

declare(strict_types=1);

namespace ResourceController\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

abstract class AbstractModelController extends Controller
{
    /**
     * Query the database to make sure the row doesn't exist already.
     *
     * @var bool
     */
    protected $checkExisting = false;

    /**
     * The key to access when returning the 'id' within destroy.
     *
     * @var string
     */
    protected $deleteModelKey = 'id';

    /**
     * Eager load relations on the model.
     *
     * @var array
     */
    protected $load = [];

    /**
     * Should be the string for the Model class.
     *
     * @var string
     */
    protected $model;

    /**
     * The route action for the model.
     *
     * @var string
     */
    protected $modelAction = 'index';

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @var bool
     */
    protected $refresh = true;

    /**
     * The string for the Model data to be nested within.
     *
     * @var string
     */
    protected $responseKey;

    /**
     * Attributes to be skipped when pulling from validated request.
     *
     * @var array
     */
    protected $skipAttributes = [];

    /**
     * Standard response method.
     *
     * @param  string|array|object  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    abstract protected function response($data = [], $status = 200, array $headers = []);

    /**
     * Response for store/update endpoints.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    abstract protected function responseModifySuccess($data = [], $status = 200, array $headers = []);

    /**
     * Response for destroy endpoint.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    abstract protected function responseDestroySuccess($data = [], $status = 200, array $headers = []);

    /**
     * Response for when errors occur.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    abstract protected function responseError($data = [], $status = 200, array $headers = []);

    /**
     * @param  string  $action
     * @return $this
     */
    protected function setModelAction(string $action): self
    {
        $this->modelAction = $action;

        return $this;
    }

    /**
     * @param  string  $name
     * @param  \Illuminate\Http\Request|null  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function getModelParameter(string $name, Request $request = null): Model
    {
        $model = ($request ?? request())->route()->parameter($name);

        if ($model instanceof Model) {
            return $model;
        }
        if (Str::singular($this->getResponseModelKey()) === $name) {
            return $this->newModel()->findOrFail($model);
        }

        return $this->guessModelParameterFindOrFail($name, $model);
    }

    /**
     * @param  string  $name
     * @return string
     */
    protected function guessModelParameterNamespace(string $name): string
    {
        return rtrim(app()->getNamespace(), '\\');
    }

    /**
     * @param  string  $name
     * @return string
     */
    protected function guessModelParameterClass(string $name): string
    {
        return Str::studly($name);
    }

    /**
     * @param  string  $name
     * @param  mixed  $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function guessModelParameterFindOrFail(string $name, $model): Model
    {
        $guessClass = $this->guessModelParameterClass($name);
        foreach ([$this->guessModelParameterNamespace($name), $this->guessModelClassNamespace()] as $namespace) {
            $modelClass = $namespace.'\\'.$guessClass;
            if (class_exists($modelClass)) {
                return app($modelClass)->findOrFail($model);
            }
        }
        $message = "No model found when using route parameter '{$name}' with value: ".print_r($model ?? 'null', true);

        throw new ModelNotFoundException($message);
    }

    /**
     * @param  \Illuminate\Http\Request|null  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected function getUserModel(Request $request = null): Model
    {
        return $this->getModelParameter('user', $request);
    }

    /**
     * Useful if you want to ensure that the model is
     * an instanceof a specific interface/class.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     *
     * @throws \RuntimeException
     */
    protected function isModelInstanceOf(Model $model): void
    {
        // Add checks for instanceof and throw a RuntimeException
    }

    /**
     * Guess the namespace for the controller model resource.
     *
     * @return string
     */
    protected function guessModelClassNamespace(): string
    {
        if (isset($this->modelNamespace)) {
            return $this->modelNamespace;
        }

        return rtrim(app()->getNamespace(), '\\');
    }

    /**
     * Guess the model classname for thecontroller model resource.
     *
     * @return string
     */
    protected function guessModel(): string
    {
        $modelClass = $this->guessModelClassNamespace().'\\';
        $modelClass .= str_replace(['\\', 'Controller'], '', Str::studly(class_basename($this)));

        return $modelClass;
    }

    /**
     * Get a new instance of the Model.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Eloquent|\Illuminate\Database\Eloquent\Model
     */
    protected function newModel(): Model
    {
        $model = app($this->model ?? $this->guessModel());
        $this->isModelInstanceOf($model);

        return $model;
    }

    /**
     * Get the current model instance of the route parameter.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Eloquent|\Illuminate\Database\Eloquent\Model
     */
    protected function getModelInstance(Request $request = null)
    {
        return $this->getModelParameter($this->getResponseModelKey(), $request);
    }

    /**
     * Return the singular table name for the model resource.
     *
     * @return string
     */
    protected function getTableSingular(): string
    {
        return Str::singular($this->newModel()->getTable());
    }

    /**
     * Get the response key the model information will be nested in.
     *
     * @return string
     */
    protected function getResponseModelKey(): string
    {
        return $this->responseKey ?? $this->getTableSingular();
    }

    /**
     * Get a model resource response message.
     *
     * @param  $message
     * @return array
     */
    protected function getReturnMessage($message): array
    {
        return [$this->getResponseModelKey() => $message];
    }

    /**
     * Get a model resource response array key(s)/values.
     *
     * @param  Model  $instance
     * @return array
     */
    protected function getReturnKeys(Model $instance): array
    {
        $this->refreshModel($instance);
        $this->loadMissing($instance);

        return $this->getModelResponse($instance);
    }

    /**
     * Get a model resource array response.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     * @return array
     */
    protected function getModelResponse(Model $instance): array
    {
        return [$this->getResponseModelKey() => $instance->toArray()];
    }

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     */
    protected function refreshModel(Model &$instance): void
    {
        if ($this->refresh === true) {
            $instance->refresh();
        }
    }

    /**
     * Eager load relations on the model if they are not already eager loaded.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     */
    protected function loadMissing(Model &$instance): void
    {
        if (! empty($this->load)) {
            $instance->loadMissing($this->load);
        }
    }

    /**
     * Get attributes from validated request.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return array
     */
    protected function getRequestAttributes(FormRequest $request): array
    {
        $attributes = [];
        $fields = $request->validated();
        foreach ($fields as $name => $value) {
            if ($request->has($name) && ! in_array($name, $this->skipAttributes)) {
                $attributes[$name] = $value;
            }
        }

        return $attributes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    abstract public function index();

    /**
     * Gate the store route based on the request.
     *
     * @param  FormRequest  $request
     */
    protected function gateStore(FormRequest $request): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  FormRequest  $request
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    public function modelStore(FormRequest $request)
    {
        $this->setModelAction('store');

        $this->gateStore($request);

        $response = $this->checkExistingStore($request);
        if ($response instanceof HttpResponse) {
            return $response;
        }

        return $this->responseModifySuccess($this->getReturnKeys($this->modelCreate($request)));
    }

    /**
     * Get attributes/foreign keys from request,
     * save a new model and return the instance.
     *
     * @param  FormRequest  $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function modelCreate(FormRequest $request): Model
    {
        $attributes = $this->getStoreAttributes($request);
        $this->setForeignKeys($request, $attributes);

        return $this->newModel()->create($attributes);
    }

    /**
     * Set foreign keys within attributes.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param  array  $attributes
     * @return void
     */
    protected function setForeignKeys(FormRequest $request, array &$attributes): void
    {
        // Update attributes to contain foreign key values.
    }

    /**
     * Get the attributes from request for store action.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return array
     */
    protected function getStoreAttributes(FormRequest $request): array
    {
        return $this->getRequestAttributes($request);
    }

    /**
     * Return response in the event existing model is found.
     * Return checkExistingResponse or custom response.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return \Symfony\Component\HttpFoundation\Response|null|void
     */
    protected function checkExistingStore(FormRequest $request)
    {
    }

    /**
     * If an existing model is found then return this response.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    protected function checkExistingResponse()
    {
        return $this->response(['errors' => $this->getReturnMessage('Model already exists.')], 422);
    }

    /**
     * Gate the show route based on the model instance.
     *
     * @param  Model  $instance
     */
    protected function gateShow(Model $instance): void
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $this->setModelAction(__FUNCTION__);

        $instance = $this->getModelInstance($request);

        $this->gateShow($instance);

        return $this->response($this->getReturnKeys($instance));
    }

    /**
     * Gate the update route based on the request and the model instance.
     *
     * @param  FormRequest  $request
     * @param  Model  $instance
     */
    protected function gateUpdate(FormRequest $request, Model $instance): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FormRequest  $request
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    public function modelUpdate(FormRequest $request)
    {
        $this->setModelAction('update');

        $instance = $this->getModelInstance($request);

        $this->gateUpdate($request, $instance);

        if (! $this->fillAndUpdate($instance, $request)) {
            return $this->responseError(['errors' => $this->getReturnMessage('Unable to save changes.')]);
        }

        return $this->responseModifySuccess($this->getReturnKeys($instance));
    }

    /**
     * Fill in request attributes and update model resource.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return bool
     */
    protected function fillAndUpdate(Model &$instance, FormRequest $request): bool
    {
        return $instance->fill($this->getUpdateAttributes($request, $instance))->update();
    }

    /**
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     * @return array
     */
    protected function getUpdateAttributes(FormRequest $request, Model $instance): array
    {
        return $this->getRequestAttributes($request);
    }

    /**
     * Gate the destroy route based on the model instance.
     *
     * @param  Model  $instance
     */
    protected function gateDestroy(Model $instance): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        $this->setModelAction(__FUNCTION__);

        $instance = $this->getModelInstance($request);

        $this->gateDestroy($instance);

        if (! $instance->delete()) {
            return $this->responseError(array_merge([
                'success' => false,
                'errors' => $this->getReturnMessage('Unable to delete.'),
            ], $this->getReturnMessage(['id' => $instance->{$this->deleteModelKey}])));
        }

        return $this->responseDestroySuccess(array_merge([
            'success' => true,
        ], $this->getReturnMessage(['id' => $instance->{$this->deleteModelKey}])));
    }
}
