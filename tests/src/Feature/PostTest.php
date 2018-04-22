<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Feature;

use Faker\Generator;
use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EricDowell\ResourceController\Tests\Models\TestPost;
use EricDowell\ResourceController\Tests\Models\TestUser;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group morph-model
     */
    public function testModelIndex()
    {
        $this->assertFunctionSuccess($this->get(route('post.index')), __FILE__, __FUNCTION__);
    }

    /**
     * @test
     * @group morph-model
     */
    public function testModelCreate()
    {
        $user = factory(TestUser::class)->create();
        $this->assertFunctionSuccess($this->actingAs($user)->get(route('post.create')), __FILE__, __FUNCTION__);
    }

    /**
     * @test
     * @group morph-model
     *
     * @returns TestPost|null
     */
    public function testModelStore()
    {
        $user = factory(TestUser::class)->create();
        /** @var Generator $fakery */
        $fakery = app(Generator::class);

        $body = $fakery->paragraph();
        $title = $fakery->words(3, true);

        $response = $this->actingAs($user)->post(route('post.store'), compact('title', 'body'));

        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__, 302);

        $response->assertRedirect(url(route('post.index')));

        return TestPost::whereTitle($title)->first();
    }

    /**
     * @depends testModelStore
     *
     * @test
     * @group single-model
     *
     * @param $model
     */
    public function testStoredModelInstance($model)
    {
        $this->assertInstanceOf(TestPost::class, $model);
    }

    /**
     * @test
     * @group morph-model
     */
    public function testModelUpdate()
    {
        $this->markTestIncomplete();
    }

    /**
     * @depends testModelUpdate
     *
     * @test
     * @group single-model
     *
     * @param $model
     */
    public function testUpdatedModelInstance($model)
    {
        $this->assertInstanceOf(TestPost::class, $model);
    }

    /**
     * @test
     * @group morph-model
     */
    public function testModelShow()
    {
        $this->markTestIncomplete();
    }
}
