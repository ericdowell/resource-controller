<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Feature;

use EricDowell\ResourceController\Tests\TestCase;
use Faker\Generator;
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
     */
    public function testModelStoreUpdate()
    {
        $user = factory(TestUser::class)->create();
        /** @var Generator $fakery */
        $fakery = app(Generator::class);

        $name = $fakery->name;
        $email = $fakery->unique()->safeEmail;
        $password = $password_confirmation = 'secret';

        $response = $this->actingAs($user)->post(route('user.store'), compact('name', 'email', 'password', 'password_confirmation'));

        $this->assertFunctionSuccess($response, __FILE__, __FUNCTION__, 302);

        $response->assertRedirect(url(route('user.index')));
    }

    /**
     * @test
     * @group single-model
     */
    public function testModelShow()
    {
        $this->markTestIncomplete();
    }
}
