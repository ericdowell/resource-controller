<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits\With;

use Closure;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model as Eloquent;

trait ModelResource
{
    use Model;
    use ModelResourceProps;

    /**
     * Create a new controller instance.
     *
     * @param Router $router
     */
    final public function __construct(Router $router)
    {
        $this->beginningConstruct();

        $middleware = $this->modelMiddleware;
        $this->mergeData = $this->checkModels()->generateDefaults($router->current());

        if (! in_array($this->formAction, $this->publicActions)) {
            $middleware = array_merge($this->authMiddleware, $middleware);
        }
        $this->middleware($middleware);

        $this->finishConstruct();
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
     * A place to complete any addition constructor required logic.
     */
    protected function beginningConstruct(): void
    {
    }

    /**
     * A place to complete any addition constructor required logic.
     */
    protected function finishConstruct(): void
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws Throwable
     */
    public function index()
    {
        $perPage = array_get($this->paginate, 'perPage', null);
        $columns = array_get($this->paginate, 'columns', ['*']);
        $pageName = array_get($this->paginate, 'pageName', 'page');
        $page = array_get($this->paginate, 'page', null);

        $templateReference = array_get($this->paginate, 'templateReference', 'models');

        ${$templateReference} = $this->allModels()->paginate($perPage, $columns, $pageName, $page);

        return $this->finish(compact($templateReference));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     * @throws Throwable
     */
    public function create()
    {
        $route = sprintf('%s.%s', $this->type, $this->formAction);

        return $this->finish(['options' => compact('route')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function storeModel(Request $request)
    {
        $this->storeAction($request);

        return $this->finishAction('store');
    }

    /**
     * Method useful to add/update attributes for Eloquent Model before initial creation.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function beforeStoreModel(Request $request): array
    {
        return [];
    }

    /**
     * Collects attributes and creates Eloquent Model.
     *
     * @param Request $request
     *
     * @return Eloquent
     */
    protected function storeAction(Request $request): Eloquent
    {
        $attributes = array_merge($this->getModelRequestAttributes($request), $this->beforeStoreModel($request));

        $this->setUserIdAttribute($attributes, __FUNCTION__);

        return $this->modelInstance()->create($attributes);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed $id
     *
     * @return Response
     * @throws Throwable
     */
    public function show($id)
    {
        ${$this->type} = $instance = $this->findModel($id);

        return $this->finish(compact($this->type, 'instance'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  mixed $id
     *
     * @return Response
     * @throws Throwable
     */
    public function edit($id)
    {
        ${$this->type} = $instance = $this->findModel($id);
        $options = [
            'route' => [sprintf('%s.%s', $this->type, $this->formAction), $instance->getKey()],
            'method' => $this->editMethod(),
        ];

        return $this->finish(compact($this->type, 'instance', 'options'));
    }

    /**
     * @return string
     */
    protected function editMethod(): string
    {
        if (isset($this->editMethod)) {
            return $this->editMethod;
        }

        return $this->allowUpsert() ? 'patch' : 'put';
    }

    /**
     * Method useful to add/update attributes for Eloquent Model before storage update.
     *
     * @param Request $request
     * @param Eloquent $instance
     *
     * @return void
     */
    protected function beforeModelUpdate(Request $request, Eloquent &$instance): void
    {
    }

    /**
     * Updates/Upserts attributes based on request for Eloquent Model.
     *
     * @param Request $request
     * @param Eloquent $instance
     *
     * @return bool
     */
    protected function updateUpsertAction(Request $request, Eloquent $instance): bool
    {
        if ($this->allowUpsert() && $request->isMethod('patch')) {
            return $this->upsertAction($request, $instance);
        }

        return $this->updateAction($request, $instance);
    }

    /**
     * @return bool
     */
    protected function allowUpsert(): bool
    {
        if (isset($this->allowUpsert)) {
            return $this->allowUpsert;
        }

        return true;
    }

    /**
     * Updates attributes based on request for Eloquent Model.
     *
     * @param Request $request
     * @param Eloquent $instance
     *
     * @return bool
     */
    protected function updateAction(Request $request, Eloquent $instance): bool
    {
        return $instance->update($this->getModelRequestAttributes($request, $instance)) ?? false;
    }

    /**
     * Upsert attributes based on request for Eloquent Model.
     *
     * @param Request $request
     * @param Eloquent $instance
     *
     * @return bool
     */
    protected function upsertAction(Request $request, Eloquent $instance): bool
    {
        $attributes = $this->upsertAttributes($request, $instance);

        return $instance->update($attributes) ?? false;
    }

    /**
     * @param Request $request
     * @param Eloquent $instance
     *
     * @return array
     */
    protected function upsertAttributes(Request $request, Eloquent $instance): array
    {
        $data = $request->except($this->upsertExcept());
        if ($request instanceof FormRequest) {
            $data = array_except($request->validated(), $this->upsertExcept());
        }

        return $this->getModelAttributes($instance, $data, true);
    }

    /**
     * @return array
     */
    protected function upsertExcept(): array
    {
        if (isset($this->upsertExcept)) {
            return $this->upsertExcept;
        }

        return [];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  mixed $id
     *`
     * @return RedirectResponse
     */
    public function updateModel(Request $request, $id)
    {
        tap($this->findModel($id), $this->updateModelCallback($request));

        return $this->finishAction('update');
    }

    /**
     * @param Request $request
     * @return Closure
     */
    protected function updateModelCallback(Request $request): Closure
    {
        return function (Eloquent $instance) use ($request) {
            $this->beforeModelUpdate($request, $instance);
            $this->setUserIdAttribute($instance, 'updateModel');
            $this->updateUpsertAction($request, $instance);
        };
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
        tap($this->findModel($id), function (Eloquent $instance) {
            $instance->delete();
        });

        return $this->finishAction(__FUNCTION__);
    }

    /**
     * Render html based on template, data and global mergeData.
     *
     * @param array $data
     *
     * @return string
     * @throws Throwable
     */
    protected function render(array $data = [])
    {
        return view($this->template, $data, $this->mergeData)->render();
    }

    /**
     * Returns html response.
     *
     * @param array $data
     * @param int $status
     * @param array $headers
     *
     * @return Response
     * @throws Throwable
     */
    protected function finish(array $data = [], $status = 200, array $headers = [])
    {
        return response($this->render($data), $status, $headers);
    }

    /**
     * @param array $errors
     * @param array|null $inputs - pass empty array for none, null for all or array of inputs.
     * @param string $key
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBack(array $errors = [], array $inputs = null, $key = 'default')
    {
        return redirect()->back()->withInput($inputs)->withErrors($errors, $key);
    }

    /**
     * Returns redirects back to index route.
     *
     * @param string $action
     *
     * @return RedirectResponse
     */
    protected function finishAction($action)
    {
        return redirect()->route(sprintf('%s.index', $this->type));
    }
}
