<?php

namespace EricDowell\ResourceController\Tests\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use EricDowell\ResourceController\Tests\Models\Post;
use EricDowell\ResourceController\Tests\Http\Requests\PostRequest;

class PostController extends TextController
{
    /**
     * Name of the affected Eloquent model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Store a newly created resource in storage.
     *
     * @param  PostRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PostRequest $request): RedirectResponse
    {
        return $this->storeModel($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PostRequest $request
     * @param  mixed $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PostRequest $request, $id): RedirectResponse
    {
        return $this->updateModel($request, $id);
    }
}
