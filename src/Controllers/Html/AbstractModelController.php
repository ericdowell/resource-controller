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
        $search = [
            '{',
            '}',
        ];
        $replace = [
            '',
            '',
        ];

        return str_replace($search, $replace, Route::currentRouteName());
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
        $this->additionalViewData($data);

        return response()->view($this->getTemplateName(), $data, $status, $headers);
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
        $instance = Arr::get($data, $this->getResponseModelKey());
        $inputs = Arr::except($data, $this->getResponseModelKey());
        $to = action([
            static::class,
            'show',
        ], [$this->getResponseModelKey() => $instance->id]);

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
        $to = action([
            static::class,
            'index',
        ]);

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
        return redirect()->back($status, $headers, $fallback)->withErrors(Arr::get($data, 'errors'));
    }
}