<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Feature;

use Faker\Generator;
use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EricDowell\ResourceController\Tests\Models\TestUser;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group single-model
     */
    public function testModelIndex()
    {
        $this->assertFunctionSuccess($this->get(route('user.index')), __FILE__, __FUNCTION__);
    }

    /**
     * @test
     * @group single-model
     */
    public function testModelCreate()
    {
        $user = factory(TestUser::class)->create();
        $this->assertFunctionSuccess($this->actingAs($user)->get(route('user.create')), __FILE__, __FUNCTION__);
    }

    /**
     * @test
     * @group single-model
     *
     * @returns TestUser|null
     */
    public function testModelStoreAndShow()
    {
        $user = factory(TestUser::class)->create();
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $name = $faker->name;
        $email = $faker->unique()->safeEmail;
        $password = 'secret';

        $response = $this->actingAs($user)->post(route('user.store'), compact('name', 'email', 'password'));

        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__, 302);

        $response->assertRedirect(url(route('user.index')));

        $this->assertNull(TestUser::wherePassword($password)->first());

        $model = TestUser::whereEmail($email)->first();

        $this->assertInstanceOf(TestUser::class, $model);

        $this->assertFunctionSuccess($this->get(route('user.show', $model->id)), __FILE__, __FUNCTION__);
    }

    /**
     * @test
     * @group single-model
     */
    public function testModelUpdate()
    {
        /** @var TestUser $user */
        $user = factory(TestUser::class)->create();

        /** @var Generator $faker */
        $faker = app(Generator::class);

        $name = $faker->name;
        $email = $faker->unique()->safeEmail;
        $password = 'secret';

        $response = $this->actingAs($user)->put(route('user.update', $user->id), compact('name', 'email', 'password'));

        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__, 302);

        $response->assertRedirect(url(route('user.index')));

        $user->refresh();

        $this->assertSame($name, $user->name);
        $this->assertSame($email, $user->email);
    }
}
