<?php

declare(strict_types=1);

namespace ResourceController\Tests\Feature;

use Illuminate\Support\Facades\Route;
use ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use ResourceController\Tests\Models\TestUser;
use ResourceController\Tests\Models\TestPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ResourceController\Tests\Traits\WithRequestHeaders;

/**
 * @group test_post
 * @group controller
 * @group feature
 */
class PostTest extends TestCase
{
    use RefreshDatabase, WithRequestHeaders, WithFaker;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Route::group(['namespace' => 'ResourceController\Tests\Http\Controllers'], function () {
            Route::apiResource('test_user.test_post', 'TestPostController');
        });
    }

    /**
     * @group index
     *
     * @return void
     */
    public function testPostIndex(): void
    {
        /** @var \ResourceController\Tests\Models\TestUser $user */
        $user = factory(TestUser::class)->create();
        factory(TestPost::class, 5)->create([
            'test_user_id' => $user->id,
        ]);
        $user->refresh();
        $response = $this->get(route('test_user.test_post.index', ['test_user' => $user->id]));

        $response->assertStatus(200);

        foreach ($user->posts as $post) {
            $response->assertJsonFragment($post->toArray());
        }
    }

    /**
     * @group store
     *
     * @return void
     */
    public function testPostStore(): void
    {
        /** @var \ResourceController\Tests\Models\TestUser $user */
        $user = factory(TestUser::class)->create();
        /** @var \ResourceController\Tests\Models\TestPost $post */
        $post = factory(TestPost::class)->make();

        $data = $post->only([
            'title',
            'body',
        ]);

        $response = $this->post(route('test_user.test_post.store', ['test_user' => $user->id]), $data,
            $this->getAcceptJsonFormHeaders());

        $response->assertJsonMissingValidationErrors();
        $this->assertFunctionSuccessJson($response, __FILE__, __FUNCTION__.'.store');
        $response->assertStatus(200);
        $response->assertJson(['test_post' => $data]);

        $id = $response->json('test_post.id');

        $this->assertIsNumeric($id);

        $post = TestPost::whereId($id)->whereTestUserId($user->id)->first();

        $this->assertInstanceOf(TestPost::class, $post);
    }

    /**
     * @group show
     *
     * @return void
     */
    public function testPostShow(): void
    {
        /** @var \ResourceController\Tests\Models\TestUser $user */
        $user = factory(TestUser::class)->create();
        /** @var \ResourceController\Tests\Models\TestPost $post */
        $post = factory(TestPost::class)->create(['test_user_id' => $user->id]);

        $response = $this->get(route('test_user.test_post.show', [
            'test_user' => $user->id,
            'test_post' => $post->id,
        ]));

        $response->assertStatus(200);

        $id = $response->json('test_post.id');
        $this->assertIsNumeric($id);
        $this->assertSame($post->id, $id);

        $response->assertJsonFragment(['test_post' => $post->refresh()->toArray()]);
    }

    /**
     * @group update
     *
     * @return void
     */
    public function testPostUpdate(): void
    {
        /** @var \ResourceController\Tests\Models\TestUser $user */
        $user = factory(TestUser::class)->create();
        /** @var \ResourceController\Tests\Models\TestPost $post */
        $post = factory(TestPost::class)->create([
            'test_user_id' => $user->id,
        ]);

        $data = [
            'is_published' => true,
        ];

        $response = $this->put(route('test_user.test_post.update', [
            'test_user' => $user->id,
            'test_post' => $post->id,
        ]), $data, $this->getAcceptJsonFormHeaders());

        $response->assertJsonMissingValidationErrors();
        $response->assertStatus(200);

        $id = $response->json('test_post.id');
        $this->assertIsNumeric($id);
        $this->assertSame($post->id, $id);

        $response->assertJsonFragment($data);
        $response->assertJson(['test_post' => $post->refresh()->toArray()]);
    }

    /**
     * @group destroy
     *
     * @return void
     */
    public function testPostDestroy(): void
    {
        /** @var \ResourceController\Tests\Models\TestUser $user */
        $user = factory(TestUser::class)->create();
        /** @var \ResourceController\Tests\Models\TestPost $post */
        $post = factory(TestPost::class)->create([
            'test_user_id' => $user->id,
        ]);

        $response = $this->delete(route('test_user.test_post.destroy', [
            'test_user' => $user->id,
            'test_post' => $post->id,
        ]), [], $this->getAcceptJsonFormHeaders());

        $response->assertStatus(200);

        $id = $response->json('test_post.id');
        $this->assertIsNumeric($id);
        $this->assertSame($post->id, $id);

        $response->assertJson([
            'success' => true,
            'test_post' => ['id' => $post->id],
        ]);
    }
}