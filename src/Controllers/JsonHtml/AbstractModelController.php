<?php

declare(strict_types=1);

namespace ResourceController\Controllers\JsonHtml;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use ResourceController\Controllers\JsonApi\AbstractModelController as JsonAbstractModelController;
use ResourceController\Traits\Controllers\Response\WithHtml;

abstract class AbstractModelController extends JsonAbstractModelController
{
    use WithHtml {
        WithHtml::response as responseHtml;
    }

    /**
     * @var array
     */
    const CONVERT_STATUS_CODES = [
        301,
        302,
    ];

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     * @return array
     */
    protected function getModelResponse(Model $instance): array
    {
        return [$this->getResponseModelKey() => $instance];
    }

    /**
     * @return string
     */
    protected function getTemplateName(): string
    {
        return Route::currentRouteName();
    }

    /**
     * Add additional view data.
     *
     * @param  array  $data
     * @return void
     */
    protected function additionalViewData(array &$data): void
    {
        //
    }

    /**
     * Standard JSON/HTML response method. Can override to return other content types.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    protected function response($data = [], $status = null, array $headers = [])
    {
        if (request()->expectsJson()) {
            return parent::response($data, $status ?? 200, $headers);
        }

        return $this->responseHtml($data, $status ?? 200, $headers);
    }

    /**
     * JSON/HTML response for store/update endpoints.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function responseModifySuccess($data = [], $status = null, array $headers = [])
    {
        if (request()->expectsJson()) {
            return parent::responseModifySuccess($data, $status ?? 200, $headers);
        }

        return $this->redirectModifySuccess($data, $status ?? 302, $headers);
    }

    /**
     * JSON/HTML response for destroy endpoint.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function responseDestroySuccess($data = [], $status = null, array $headers = [])
    {
        if (request()->expectsJson()) {
            return parent::responseDestroySuccess($data, $status ?? 200, $headers);
        }

        return $this->redirectDestroySuccess($data, $status ?? 302, $headers);
    }

    /**
     * JSON/HTML response for when errors occur.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function responseError($data = [], $status = null, array $headers = [])
    {
        if (request()->expectsJson()) {
            return parent::responseError($data, $status ?? 200, $headers);
        }

        return $this->redirectBackError($data, $status ?? 302, $headers);
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
     * @throws \Throwable|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
     * @throws \Throwable|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
}
