<?php

declare(strict_types=1);

namespace ResourceController\Tests\Feature;

use Faker\Generator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use ResourceController\Tests\TestCase;
use ResourceController\Tests\Models\TestUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Route::group(['namespace' => 'ResourceController\Tests\Http\Controllers'], function () {
            Route::resource('user', 'TestUserController');
        });
    }

    /**
     * @test
     * @group feature
     * @group single-model
     */
    public function testModelIndex()
    {
        $this->assertFunctionSuccess($this->get(route('user.index')), __FILE__, __FUNCTION__);
    }

    /**
     * @test
     * @group feature
     * @group single-model
     */
    public function testModelCreate()
    {
        $user = factory(TestUser::class)->create();
        $this->assertFunctionSuccess($this->actingAs($user)->get(route('user.create')), __FILE__, __FUNCTION__);
    }

    /**
     * @test
     * @group feature
     * @group single-model
     *
     * @returns TestUser|null
     */
    public function testModelStoreShowEdit()
    {
        $user = factory(TestUser::class)->create();
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $name = $faker->name;
        $email = $faker->unique()->safeEmail;
        $password = $password_confirmation = 'password';

        $response = $this->actingAs($user)->post(route('user.store'), compact('name', 'email', 'password', 'password_confirmation'));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.store', 302);

        $model = TestUser::whereEmail($email)->first();

        $response->assertRedirect(url(route('user.show', [$model->id])));

        $this->assertNull(TestUser::wherePassword($password)->first());

        $this->assertInstanceOf(TestUser::class, $model);

        $this->assertFunctionSuccess($this->get(route('user.show', $model->id)), __FILE__, __FUNCTION__.'.show');
        $this->assertFunctionSuccess($this->get(route('user.edit', $model->id)), __FILE__, __FUNCTION__.'.edit');
    }

    /**
     * @test
     * @group feature
     * @group single-model
     */
    public function testModelUpdateAndDestroy()
    {
        /** @var TestUser $user */
        $user = factory(TestUser::class)->create();

        /** @var Generator $faker */
        $faker = app(Generator::class);

        $name = $faker->name;
        $email = $faker->unique()->safeEmail;
        $password = $password_confirmation = 'password1234';

        $response = $this->actingAs($user)->put(route('user.update', $user->id), compact('name', 'email', 'password', 'password_confirmation'));
        // Would in normal cases be 'edit', but there's no 'back'/'previous', so home.index is what is used.
        $response->assertRedirect(url(route('user.show', [$user->id])));

        $user->refresh();

        $this->assertTrue(Hash::check($password, $user->password), 'Password was NOT updated, when it should have been.');
        $this->assertNull(TestUser::wherePassword($password)->first(), 'The password was saved in the database as plaintext!');

        $response = $this->actingAs($user)->delete(route('user.destroy', $user->id));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.destroy', 302);
        $response->assertRedirect(url(route('user.index')));
    }
}
