<?php

declare(strict_types=1);

namespace ResourceController\Traits\Controllers;

use Illuminate\Database\Eloquent\Model;

trait PaginateIndex
{
    /**
     * Get the response key the model information will be nested in.
     *
     * @return string
     */
    abstract protected function getResponseModelKey(): string;

    /**
     * Standard response method.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    abstract protected function response($data = [], $status = 200, array $headers = []);

    /**
     * Get a new instance of the Model.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Eloquent|\Illuminate\Database\Eloquent\Model
     */
    abstract protected function newModel(): Model;

    /**
     * Display a listing of the resource.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (method_exists($this, 'setModelAction')) {
            $this->setModelAction(__FUNCTION__);
        }

        $modelKey = $this->getResponseModelKey();

        if (request()->expectsJson()) {
            return $this->response([
                $modelKey => $this->newModel()->paginate(),
            ]);
        }

        return $this->response([
            $modelKey => $this->newModel()->paginate(),
            'modelKey' => $modelKey,
        ]);
    }
}
