<?php

namespace EricDowell\ResourceController\Http\Controllers;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route as CurrentRoute;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class ModelMorphController extends Controller
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
     * @var CurrentRoute
     */
    protected $route;

    /**
     * @var array
     */
    protected $mergeData = [];

    /**
     * Name of the affected Eloquent model.
     *
     * @var string
     */
    protected $model;

    /**
     * @var Model|Builder
     */
    protected $modelInstance;

    /**
     * @var string
     */
    protected $morphModel;

    /**
     * @var bool
     */
    protected $withUser = true;

    /**
     * @var int
     */
    protected $perPage = 10;

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
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->setupModel()->setupDefaults();

        if (! in_array($this->formAction, $this->publicActions)) {
            $this->middleware('auth');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws Throwable
     */
    public function index()
    {
        return $this->render(['all' => $this->allMorphModels()->paginate($this->perPage)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     * @throws Throwable
     */
    public function create(): Response
    {
        $route = sprintf('%s.%s', $this->type, $this->formAction);

        return $this->render(['options' => compact('route')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FormRequest $request
     *
     * @return RedirectResponse
     */
    public function storeModel(FormRequest $request): RedirectResponse
    {
        $model = $this->modelInstance->create($this->getModelAttributes($request));

        $morphType = $this->getMorphType();

        $attributes = array_merge($this->beforeStoreModel($request), [
            "{$morphType}_type" => $this->type,
            "{$morphType}_id" => $model->id,
        ]);

        $instance = forward_static_call([$this->morphModel, 'create'], $attributes);

        if (! $instance instanceof Model) {
            return redirect()->back()->withInput()->withErrors('Something went wrong.');
        }

        return $this->redirectToIndex();
    }

    /**
     * @param FormRequest $request
     *
     * @return array
     */
    protected function beforeStoreModel(FormRequest $request): array
    {
        return [];
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed $id
     *
     * @return Response
     * @throws Throwable
     */
    public function show($id): Response
    {
        ${$this->type} = $instance = $this->findMorphModel($id);

        return $this->render(compact($this->type, 'instance'));
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
        $instance = $this->findMorphModel($id);
        $options = [
            'route' => [sprintf('%s.%s', $this->type, $this->formAction), $instance->getKey()],
            'method' => 'put',
        ];

        return $this->render(compact('instance', 'options'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FormRequest $request
     * @param  mixed $id
     *
     * @return RedirectResponse
     */
    public function updateModel(FormRequest $request, $id): RedirectResponse
    {
        $instance = $this->findMorphModel($id);

        $this->beforeModelUpdate($request, $instance);

        $updated = $instance->{$this->getMorphType()}->update($this->getModelAttributes($request));
        if (! $updated) {
            return redirect()->back()->withInput()->withErrors('Something went wrong.');
        }

        return $this->redirectToIndex();
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
     * Remove the specified resource from storage.
     *
     * @param  mixed $id
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy($id): RedirectResponse
    {
        $instance = $this->findMorphModel($id);
        /** @var Model $model */
        $model = $instance->{$this->getMorphType()};

        $model->delete();
        $instance->delete();

        return $this->redirectToIndex();
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
     * @return RedirectResponse
     */
    protected function redirectToIndex(): RedirectResponse
    {
        return redirect()->route($this->type.'.index');
    }

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     *
     * @return Response
     * @throws Throwable
     */
    protected function render(array $data = [], $status = 200, array $headers = []): Response
    {
        $html = view($this->template, $data, $this->mergeData)->render();

        return response($html, $status, $headers);
    }

    /**
     * @return string
     */
    protected function getMorphType(): string
    {
        return snake_case(str_replace('\\', '', class_basename($this->morphModel)));
    }

    /**
     * @return Builder
     */
    protected function allMorphModels(): Builder
    {
        $morphType = $this->getMorphType();

        $query = forward_static_call([$this->morphModel, 'where'], "{$morphType}_type", str_singular($this->type));

        if (! $this->withUser) {
            return $query;
        }

        return $query->with('user');
    }

    /**
     * @param mixed $id
     *
     * @return Model
     */
    protected function findMorphModel($id): Model
    {
        if (! $this->withUser) {
            return forward_static_call([$this->morphModel, 'findOrFail'], $id);
        }

        return forward_static_call([$this->morphModel, 'with'], 'user')->findOrFail($id);
    }

    /**
     * @param array $items
     *
     * @return $this
     */
    protected function pushToDefaults(array $items): self
    {
        $this->mergeData = array_merge($this->mergeData, $items);

        return $this;
    }

    /**
     * @return $this
     * @throws ModelNotFoundException
     */
    protected function setupModel(): self
    {
        /** @var ModelNotFoundException $modelException */
        $modelException = with(new ModelNotFoundException())->setModel($this->model);
        if (! class_exists($modelException->getModel())) {
            throw $modelException;
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function setupDefaults(): self
    {
        $this->route = Route::current();
        if (method_exists($this->route, 'getName') && ! empty($this->route->getName())) {
            $this->template = $this->route->getName();

            $this->setTypeAndFormAction()->setTypeName()->setModelInstance();
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function setTypeAndFormAction(): self
    {
        list($type, $action) = explode('.', $this->template);

        if (array_key_exists($action, $this->actionMap)) {
            $action = $this->actionMap[$action];
        }
        $this->type = $type;
        $this->formAction = $action;
        $btnMessage = sprintf('%s %s', ucfirst($this->formAction), ucfirst($this->type));
        $formHeader = ($action === 'update' ? ucfirst($this->formAction) : 'Create').' '.ucfirst($this->type);

        $data = compact('type', 'btnMessage', 'action', 'formHeader');

        return $this->pushToDefaults($data);
    }

    /**
     * @return $this
     */
    protected function setTypeName(): self
    {
        $this->typeName = $typeName = str_plural(ucfirst($this->type));

        return $this->pushToDefaults(compact('typeName'));
    }

    /**
     * @return $this
     */
    protected function setModelInstance(): self
    {
        $this->modelInstance = $instance = new $this->model();

        return $this->pushToDefaults(compact('instance'));
    }
}
