<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests;

use Illuminate\Foundation\Testing\TestResponse;
use EricDowell\ResourceController\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Orchestra\Testbench\TestCase as SupportTestCase;
use EricDowell\ResourceController\Tests\Models\TestPost;
use EricDowell\ResourceController\Tests\Traits\LoadTestConfiguration;

class TestCase extends SupportTestCase
{
    use LoadTestConfiguration;

    /**
     * @var array
     */
    protected $morphMap = [
        'post' => TestPost::class,
    ];

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $basePath = dirname(__DIR__);

        $this->loadMigrationsFrom($basePath.'/database/migrations');
        $this->withFactories($basePath.'/database/factories');

        Relation::morphMap($this->morphMap);
    }

    /**
     * @param TestResponse $response
     * @param string $file
     * @param string $function
     * @param int $statusCode
     */
    protected function assertFunctionSuccess($response, $file, $function, $statusCode = 200)
    {
        $this->assertFunctionWithStatus($response, $file, $function, $statusCode);
    }

    /**
     * @param TestResponse $response
     * @param string $file
     * @param string $function
     * @param int $statusCode
     */
    protected function assertFunctionFailure($response, $file, $function, $statusCode = 500)
    {
        $this->assertFunctionSuccess($response, $file, $function, $statusCode);
    }

    /**
     * @param TestResponse $response
     * @param string $file
     * @param string $function
     * @param int $statusCode
     */
    private function assertFunctionWithStatus($response, $file, $function, $statusCode = null)
    {
        $filename = __DIR__.'/error-html/'.basename($file, '.php').'.'.$function.'.html';
        if (file_exists($filename)) {
            @unlink($filename);
        }
        if ($response->getStatusCode() !== $statusCode) {
            file_put_contents($filename, $response->getContent());
        }
        $response->assertStatus($statusCode);
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}
