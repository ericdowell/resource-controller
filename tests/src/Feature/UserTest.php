<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Feature;

use Faker\Generator;
use Illuminate\Support\Facades\Hash;
use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EricDowell\ResourceController\Tests\Models\TestUser;

class UserTest extends TestCase
{
    use RefreshDatabase;

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
        $password = 'secret';

        $response = $this->actingAs($user)->post(route('user.store'), compact('name', 'email', 'password'));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.store', 302);

        $response->assertRedirect(url(route('user.index')));

        $this->assertNull(TestUser::wherePassword($password)->first());

        $model = TestUser::whereEmail($email)->first();

        $this->assertInstanceOf(TestUser::class, $model);

        $this->assertFunctionSuccess($this->get(route('user.show', $model->id)), __FILE__, __FUNCTION__.'.show');
        $this->assertFunctionSuccess($this->get(route('user.edit', $model->id)), __FILE__, __FUNCTION__.'.edit');
        $this->assertFunctionSuccess($this->get(route('user-update.edit', $model->id)), __FILE__, __FUNCTION__.'.edit.put');
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
        $password = $password_confirmation = 'secret1234';
        $current_password = 'secret';

        $response = $this->actingAs($user)->put(route('user.update', $user->id), compact('name', 'email'));
        $this->assertFunctionFailure($response, __FILE__, __FUNCTION__.'.update.put');

        $response = $this->actingAs($user)->put(route('user-update.update', $user->id), compact('name', 'email'));
        $this->assertFunctionFailure($response, __FILE__, __FUNCTION__.'.update.put.edit-method-property');

        $response = $this->actingAs($user)->patch(route('user.update', $user->id), compact('name', 'email', 'password'));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.update.patch', 302);
        $response->assertRedirect(url(route('user.index')));

        $user->refresh();

        $this->assertSame($name, $user->name);
        $this->assertSame($email, $user->email);
        $this->assertNull(TestUser::wherePassword($password)->first());
        $this->assertFalse(Hash::check($password, $user->password));

        $response = $this->actingAs($user)->put(route('user.password-update', $user->id), compact('password', 'password_confirmation', 'current_password'));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.password-update', 302);
        $response->assertRedirect(url(route('user.index')));

        $user->refresh();

        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertNull(TestUser::wherePassword($password)->first());

        $response = $this->actingAs($user)->delete(route('user.destroy', $user->id));
        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__.'.destroy', 302);
        $response->assertRedirect(url(route('user.index')));
    }
}
