<?php

namespace EricDowell\ResourceController\Tests\Feature;

use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EricDowell\ResourceController\Tests\Models\TestUser;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function testPostModelIndex()
    {
        $response = $this->get('/post');

        if ($response->getStatusCode() != 200) {
            file_put_contents(__DIR__.'/error-html/'.__FUNCTION__.'.html', $response->getContent());
        }
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function testPostModelCreate()
    {
        $user = factory(TestUser::class)->create();
        $response = $this->actingAs($user)->get('/post/create');

        if ($response->getStatusCode() != 200) {
            file_put_contents(__DIR__.'/error-html/'.__FUNCTION__.'.html', $response->getContent());
        }
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function testPostModelStoreUpdate()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function testPostModelShow()
    {
        $this->markTestIncomplete();
    }
}
