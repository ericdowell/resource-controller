<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Feature;

use Faker\Generator;
use Illuminate\Support\Facades\Route;
use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EricDowell\ResourceController\Tests\Models\TestPost;
use EricDowell\ResourceController\Tests\Models\TestText;
use EricDowell\ResourceController\Tests\Models\TestUser;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        Route::group(['namespace' => 'EricDowell\ResourceController\Tests\Http\Controllers'], function () {
            Route::resource('post', 'TestPostController');
        });
    }

    /**
     * @test
     * @group feature
     * @group morph-model
     */
    public function testModelIndex()
    {
        $this->assertFunctionSuccess($this->get(route('post.index')), __FILE__, __FUNCTION__);
    }

    /**
     * @test
     * @group feature
     * @group morph-model
     */
    public function testModelCreate()
    {
        $user = factory(TestUser::class)->create();
        $this->assertFunctionSuccess($this->actingAs($user)->get(route('post.create')), __FILE__, __FUNCTION__);
    }

    /**
     * @test
     * @group feature
     * @group morph-model
     *
     * @returns TestPost|null
     */
    public function testModelStoreShowEdit()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $body = $faker->paragraph();
        $title = $faker->words(3, true);

        $authUser = factory(TestUser::class)->create();
        $response = $this->actingAs($authUser)->post(route('post.store'), compact('title', 'body'));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.store', 302);
        $response->assertRedirect(url(route('post.index')));

        $model = TestPost::whereTitle($title)->first();

        $this->assertInstanceOf(TestPost::class, $model);

        $this->assertFunctionSuccess($this->get(route('post.show', $model->id)), __FILE__, __FUNCTION__.'.show');
        $this->assertFunctionSuccess($this->get(route('post.edit', $model->id)), __FILE__, __FUNCTION__.'.edit');
    }

    /**
     * @test
     * @group feature
     * @group morph-model
     */
    public function testModelUpdateAndDestroy()
    {
        /** @var TestText $textPost */
        $textPost = factory(TestText::class, TestPost::class)->create();

        /** @var Generator $faker */
        $faker = app(Generator::class);

        $body = $faker->paragraph();
        $title = $faker->words(3, true);
        $is_published = true;

        $authUser = factory(TestUser::class)->create();
        $response = $this->actingAs($authUser)->put(route('post.update', $textPost->id), compact('title', 'body', 'is_published'));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.update.put', 302);
        $response->assertRedirect(url(route('post.index')));

        $textPost->refresh();

        $this->assertSame($title, $textPost->text->title);
        $this->assertSame($body, $textPost->text->body);
        $this->assertTrue($textPost->text->is_published);

        $is_published = false;

        $response = $this->actingAs($authUser)->patch(route('post.update', $textPost->id), compact('is_published'));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.update.patch', 302);
        $response->assertRedirect(url(route('post.index')));

        $textPost->refresh();

        $this->assertSame($title, $textPost->text->title);
        $this->assertSame($body, $textPost->text->body);
        $this->assertFalse($textPost->text->is_published);

        $response = $this->actingAs($authUser)->delete(route('post.destroy', $textPost->id));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.destroy', 302);
        $response->assertRedirect(url(route('post.index')));
    }
}
