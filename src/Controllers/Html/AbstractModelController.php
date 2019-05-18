<?php

declare(strict_types=1);

namespace ResourceController\Controllers\Html;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use ResourceController\Controllers\JsonApi\AbstractModelController as JsonAbstractModelController;

abstract class AbstractModelController extends JsonAbstractModelController
{
    /**
     * @var array
     */
    const CONVERT_STATUS_CODES = [
        301,
        302
    ];

    /**
     * @var string
     */
    protected $routeBase;

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     * @return array
     */
    protected function getModelResponse(Model $instance): array
    {
        return [$this->getResponseModelKey() => $instance];
    }

    /**
     * @param  Model  $instance
     */
    protected function gateCreate(Model $instance): void
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (request()->expectsJson()) {
            return abort(404);
        }
        $this->setModelAction(__FUNCTION__);

        $instance = $this->newModel();

        $this->gateCreate($instance);

        return $this->response($this->getReturnKeys($instance));
    }

    /**
     * @param  Model  $instance
     */
    protected function gateEdit(Model $instance): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        if (request()->expectsJson()) {
            return abort(404);
        }
        $this->setModelAction(__FUNCTION__);

        $instance = $this->getModelInstance();

        $this->gateEdit($instance);

        return $this->response($this->getReturnKeys($instance));
    }

    /**
     * @return string
     */
    protected function getTemplateName(): string
    {
        return Route::currentRouteName();
    }

    /**
     * @param  array  $data
     * @return void
     */
    protected function additionalViewData(array &$data): void
    {
        if (! isset($data['modelAction'])) {
            $data['modelAction'] = $this->modelAction;
        }
        if (! isset($data['modelKey'])) {
            $data['modelKey'] = $this->getResponseModelKey();
        }
        if (! isset($data['routeBase'])) {
            $data['routeBase'] = $this->routeBase;
        }
    }

    /**
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\Response
     */
    protected function response($data = [], $status = 200, array $headers = [])
    {
        if (request()->expectsJson()) {
            return parent::response($data, in_array($status, static::CONVERT_STATUS_CODES) ? 200 : $status, $headers);
        }
        $this->additionalViewData($data);

        $template = $this->getTemplateName();

        return response()->view($template, $data + compact('template'), $status, $headers);
    }

    /**
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    protected function responseModifySuccess($data = [], $status = 302, array $headers = [], $secure = null)
    {
        if (request()->expectsJson()) {
            return parent::responseModifySuccess($data, $status, $headers);
        }
        $instance = Arr::get($data, $this->getResponseModelKey());
        $inputs = Arr::except($data, $this->getResponseModelKey());
        $to = action([static::class, 'show',], [$this->getResponseModelKey() => $instance->id]);

        return redirect($to, $status, $headers, $secure)->withInput($inputs);
    }

    /**
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    protected function responseDestroySuccess($data = [], $status = 302, array $headers = [], $secure = null)
    {
        if (request()->expectsJson()) {
            return parent::responseDestroySuccess($data, $status, $headers);
        }
        $to = action([static::class, 'index',]);

        return redirect($to, $status, $headers, $secure)->withInput($data);
    }

    /**
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  bool  $fallback
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
     */
    protected function responseError($data = [], $status = 302, array $headers = [], $fallback = false)
    {
        if (request()->expectsJson()) {
            return parent::responseError($data, $status, $headers);
        }

        return redirect()->back($status, $headers, $fallback)->withErrors(Arr::get($data, 'errors'));
    }
}
