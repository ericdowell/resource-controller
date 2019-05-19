<?php

namespace ResourceController\Traits\Controllers\Response;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

trait WithHtml
{
    /**
     * Get the response key the model information will be nested in.
     *
     * @return string
     */
    abstract protected function getResponseModelKey(): string;

    /**
     * @param  array  $data
     * @return void
     */
    abstract protected function additionalViewData(array &$data): void;

    /**
     * @return string
     */
    protected function getTemplateName(): string
    {
        return Route::currentRouteName();
    }

    /**
     * @param  array  $data
     * @return array|string
     * @throws \Throwable
     */
    protected function renderHtml(array $data = [])
    {
        $template = $this->getTemplateName();

        return view($template, $data + compact('template'))->render();
    }

    /**
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\Response
     * @throws \Throwable
     */
    protected function response($data = [], $status = 200, array $headers = [])
    {
        $this->additionalViewData($data);

        return response()->make($this->renderHtml($data), $status, $headers);
    }

    /**
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    protected function redirectModifySuccess($data = [], $status = 302, array $headers = [], $secure = null)
    {
        $instance = Arr::get($data, $this->getResponseModelKey());
        $inputs = Arr::except($data, $this->getResponseModelKey());
        $to = action([static::class, 'show'], [$this->getResponseModelKey() => $instance->id]);

        return redirect($to, $status, $headers, $secure)->withInput($inputs);
    }

    /**
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    protected function redirectDestroySuccess($data = [], $status = 302, array $headers = [], $secure = null)
    {
        $to = action([static::class, 'index']);

        return redirect($to, $status, $headers, $secure)->withInput($data);
    }

    /**
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  bool  $fallback
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
     */
    protected function redirectBackError($data = [], $status = 302, array $headers = [], $fallback = false)
    {
        return redirect()->back($status, $headers, $fallback)->withErrors(Arr::get($data, 'errors'));
    }
}