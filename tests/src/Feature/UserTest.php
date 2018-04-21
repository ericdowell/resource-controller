<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Feature;

use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EricDowell\ResourceController\Tests\Models\TestUser;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function testModelIndex()
    {
        $response = $this->get('/user');

        if ($response->getStatusCode() != 200) {
            file_put_contents(__DIR__.'/error-html/'.basename(__FILE__, '.php').'.'.__FUNCTION__.'.html', $response->getContent());
        }
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function testModelCreate()
    {
        $user = factory(TestUser::class)->create();
        $response = $this->actingAs($user)->get('/user/create');

        if ($response->getStatusCode() != 200) {
            file_put_contents(__DIR__.'/error-html/'.basename(__FILE__, '.php').'.'.__FUNCTION__.'.html', $response->getContent());
        }
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function testModelStoreUpdate()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function testModelShow()
    {
        $this->markTestIncomplete();
    }
}
