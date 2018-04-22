<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use EricDowell\ResourceController\Tests\Models\TestPost;
use EricDowell\ResourceController\Tests\Models\TestText;
use EricDowell\ResourceController\Tests\Http\Requests\TestPostRequest;
use EricDowell\ResourceController\Http\Controllers\ModelMorphController;

class TestPostController extends ModelMorphController
{
    /**
     * Complete name/namespace of parent morph Eloquent Model.
     *
     * @var string
     */
    protected $morphModel = TestText::class;
    /**
     * Complete name/namespace of the Eloquent Model.
     *
     * @var string
     */
    protected $model = TestPost::class;

    /**
     * Store a newly created resource in storage.
     *
     * @param  TestPostRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TestPostRequest $request): RedirectResponse
    {
        return $this->storeModel($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TestPostRequest $request
     * @param  mixed $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TestPostRequest $request, $id): RedirectResponse
    {
        return $this->updateModel($request, $id);
    }
}
