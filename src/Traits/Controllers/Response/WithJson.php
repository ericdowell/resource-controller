<?php

declare(strict_types=1);

namespace ResourceController\Traits\Controllers\Response;

trait WithJson
{
    /**
     * Standard JSON response method.
     *
     * @param  string|array|object  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    protected function response($data = [], $status = 200, array $headers = [])
    {
        return response()->json($data, $status, $headers);
    }

    /**
     * JSON response for store/update endpoints.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    protected function responseModifySuccess($data = [], $status = 200, array $headers = [])
    {
        return $this->response($data, $status, $headers);
    }

    /**
     * JSON response for destroy endpoint.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    protected function responseDestroySuccess($data = [], $status = 200, array $headers = [])
    {
        return $this->response($data, $status, $headers);
    }

    /**
     * JSON response for when errors occur.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    protected function responseError($data = [], $status = 422, array $headers = [])
    {
        return $this->response($data, $status, $headers);
    }
}
