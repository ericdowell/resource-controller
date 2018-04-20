<?php

namespace EricDowell\ResourceController\Tests\Feature;

use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EricDowell\ResourceController\Tests\Models\User;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function testPostModelIndexCreate()
    {
        $status = 200;
        $user = factory(User::class)->create();

        $response = $this->get('/post');

        if ($response->getStatusCode() != 200) {
            file_put_contents(__DIR__.'/error-html/post-index.html', $response->getContent());
        }
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get('/post/create');

        if ($response->getStatusCode() != 200) {
            file_put_contents(__DIR__.'/error-html/post-create.html', $response->getContent());
        }
        $response->assertStatus($status);
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
