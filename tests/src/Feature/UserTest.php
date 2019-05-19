<?php

declare(strict_types=1);

namespace ResourceController\Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use ResourceController\Tests\Models\TestUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ResourceController\Tests\Traits\WithRequestHeaders;

/**
 * @group test_user
 * @group controller
 * @group feature
 */
class UserTest extends TestCase
{
    use RefreshDatabase, WithRequestHeaders, WithFaker;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Route::group(['namespace' => 'ResourceController\Tests\Http\Controllers'], function () {
            Route::resource('test_user', 'TestUserController');
        });
    }

    /**
     * @return void
     */
    public function testModelIndex(): void
    {
        $this->assertFunctionSuccess($this->get(route('test_user.index')), __FILE__, __FUNCTION__);
        $this->assertFunctionSuccessJson($this->get(route('test_user.index'), self::getAcceptJsonFormHeaders()), __FILE__, __FUNCTION__);
    }

    /**
     * @return void
     */
    public function testModelCreate(): void
    {
        $user = factory(TestUser::class)->create();
        $this->assertFunctionSuccess($this->actingAs($user)->get(route('test_user.create')), __FILE__, __FUNCTION__);
        $this->assertFunctionFailureJson($this->get(route('test_user.create'), self::getAcceptJsonFormHeaders()), __FILE__, __FUNCTION__.'.edit', 404);
    }

    /**
     * @returns void
     */
    public function testModelStoreShowEdit(): void
    {
        $user = factory(TestUser::class)->create();

        $name = $this->faker->name;
        $email = $this->faker->unique()->safeEmail;
        $password = $password_confirmation = 'password';

        $response = $this->actingAs($user)->post(route('test_user.store'), compact('name', 'email', 'password', 'password_confirmation'));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.store', 302);

        $model = TestUser::whereEmail($email)->first();

        $response->assertRedirect(url(route('test_user.show', [$model->id])));

        $this->assertNull(TestUser::wherePassword($password)->first());

        $this->assertInstanceOf(TestUser::class, $model);

        $this->assertFunctionSuccess($this->get(route('test_user.show', $model->id)), __FILE__, __FUNCTION__.'.show');
        $this->assertFunctionSuccess($this->get(route('test_user.edit', $model->id)), __FILE__, __FUNCTION__.'.edit');

        $this->assertFunctionSuccessJson($this->get(route('test_user.show', $model->id), self::getAcceptJsonFormHeaders()), __FILE__, __FUNCTION__.'.show');
        $this->assertFunctionFailureJson($this->get(route('test_user.edit', $model->id), self::getAcceptJsonFormHeaders()), __FILE__, __FUNCTION__.'.edit', 404);
    }

    /**
     * @return void
     */
    public function testModelUpdateAndDestroy(): void
    {
        /** @var TestUser $user */
        $user = factory(TestUser::class)->create();

        $name = $this->faker->name;
        $email = $this->faker->unique()->safeEmail;
        $password = $password_confirmation = 'password1234';

        $response = $this->actingAs($user)->put(route('test_user.update', $user->id), compact('name', 'email', 'password', 'password_confirmation'));
        // Would in normal cases be 'edit', but there's no 'back'/'previous', so home.index is what is used.
        $response->assertRedirect(url(route('test_user.show', [$user->id])));

        $user->refresh();

        $this->assertTrue(Hash::check($password, $user->password), 'Password was NOT updated, when it should have been.');
        $this->assertNull(TestUser::wherePassword($password)->first(), 'The password was saved in the database as plaintext!');

        $response = $this->actingAs($user)->delete(route('test_user.destroy', $user->id));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.destroy', 302);
        $response->assertRedirect(url(route('test_user.index')));

        /** @var TestUser $user */
        $user = factory(TestUser::class)->create();
        $response = $this->actingAs($user)->delete(route('test_user.destroy', $user->id), [], self::getAcceptJsonFormHeaders());
        $this->assertFunctionSuccessJson($response, __FILE__, __FUNCTION__.'.destroy');
        $response->assertJson(['success' => true, 'test_user' => ['id' => $user->id]]);
    }
}
