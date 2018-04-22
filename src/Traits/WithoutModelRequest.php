<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

trait WithoutModelRequest
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->storeModel($request);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    abstract public function storeModel(Request $request);

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  mixed $id
     *
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        return $this->updateModel($request, $id);
    }

    /**
     * @param  Request $request
     * @param  mixed $id
     *`
     * @return RedirectResponse
     */
    abstract public function updateModel(Request $request, $id);
}
