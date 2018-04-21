<?php

namespace EricDowell\ResourceController\Traits;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

trait WithModelResource
{
    use WithModel;

    /**
     * @var array
     */
    protected $authMiddleware = ['auth' => 'auth'];

    /**
     * @var array
     */
    protected $mergeData = [];

    /**
     * @var array
     */
    protected $modelMiddleware = [];

    /**
     * Values used for index pagination.
     *
     * @var array
     */
    protected $paginate = [];

    /**
     * Create a new controller instance.
     *
     * @param Router $router
     */
    final public function __construct(Router $router)
    {
        $middleware = $this->modelMiddleware;
        $this->mergeData = $this->checkModel($this->model)->generateDefaults($router->current());

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
     * @return \Illuminate\Routing\ControllerMiddlewareOptions
     */
    abstract public function middleware($middleware, array $options = []);

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
     * @param FormRequest $request
     *
     * @return RedirectResponse
     */
    public function storeModel(FormRequest $request)
    {
        $this->storeAction($request);

        return $this->finishAction('store');
    }

    /**
     * @param FormRequest $request
     * @return Model
     */
    protected function storeAction(FormRequest $request): Model
    {
        $attributes = array_merge($this->getModelAttributes($request), $this->beforeStoreModel($request));

        return $this->modelInstance->create($attributes);
    }

    /**
     * @param FormRequest $request
     *
     * @return array
     */
    protected function beforeStoreModel(FormRequest $request): array
    {
        if (! $this->withUser()) {
            return [];
        }

        return [
            'user_id' => $request->input('user_id') ?? data_get(auth()->user(), 'id'),
        ];
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
    public function edit($id): Response
    {
        ${$this->type} = $instance = $this->findModel($id);
        $options = [
            'route' => [sprintf('%s.%s', $this->type, $this->formAction), $instance->getKey()],
            'method' => 'put',
        ];

        return $this->finish(compact($this->type, 'instance', 'options'));
    }

    /**
     * @param FormRequest $request
     * @param Model $instance
     *
     * @return void
     */
    protected function beforeModelUpdate(FormRequest $request, Model &$instance): void
    {
    }

    /**
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param Model $instance
     *
     * @return bool
     */
    protected function updateAction(FormRequest $request, Model $instance): bool
    {
        return $instance->update($this->getModelAttributes($request)) ?? false;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FormRequest $request
     * @param  mixed $id
     *
     * @return RedirectResponse
     */
    public function updateModel(FormRequest $request, $id)
    {
        $this->findModel($id, function (Model $model) use ($request) {
            $this->beforeModelUpdate($request, $model);
            $this->updateAction($request, $model);
        });

        return $this->finishAction('update');
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
        $this->findModel($id, function (Model $model) {
            $model->delete();
        });

        return $this->finishAction(__FUNCTION__);
    }

    /**
     * @param array $data
     *
     * @return string
     * @throws Throwable
     */
    protected function render(array $data = [])
    {
        return $content = view($this->template, $data, $this->mergeData)->render();
    }

    /**
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
     * @param string $action
     *
     * @return RedirectResponse
     */
    protected function finishAction($action)
    {
        return redirect()->route($this->type.'.index');
    }
}
