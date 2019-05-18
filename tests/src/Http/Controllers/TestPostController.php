<?php

declare(strict_types=1);

namespace ResourceController\Tests\Http\Controllers;

use ResourceController\Tests\Models\TestPost;
use ResourceController\Traits\Controllers\PaginateIndex;
use ResourceController\Controllers\JsonApi\AbstractOwnerController;
use ResourceController\Tests\Http\Requests\TestPost as TestPostRequest;

class TestPostController extends AbstractOwnerController
{
    use PaginateIndex;

    /**
     * @var string
     */
    protected $model = TestPost::class;

    /**
     * @var string
     */
    protected $modelNamespace = 'ResourceController\\Tests\\Models';

    /**
     * @return string
     */
    protected function getOwnerRouteParameter(): string
    {
        return 'test_user';
    }

    /**
     * @param  \ResourceController\Tests\Http\Requests\TestPost  $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(TestPostRequest $request)
    {
        return $this->modelStore($request);
    }

    /**
     * @param  \ResourceController\Tests\Http\Requests\TestPost  $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(TestPostRequest $request)
    {
        return $this->modelUpdate($request);
    }
}