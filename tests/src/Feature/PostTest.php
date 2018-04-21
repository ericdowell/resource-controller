<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Feature;

use EricDowell\ResourceController\Tests\TestCase;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
     */
    public function testModelStoreUpdate()
    {
        $user = factory(TestUser::class)->create();
        /** @var Generator $fakery */
        $fakery = app(Generator::class);

        $body = $fakery->paragraph();
        $title = $fakery->words(3, true);

        $response = $this->actingAs($user)->post(route('post.store'), compact('title', 'body'));

        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__, 302);

        $response->assertRedirect(url(route('post.index')));
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
