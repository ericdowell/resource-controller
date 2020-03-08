<?php

declare(strict_types=1);

namespace ResourceController\Tests;

use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase as SupportTestCase;
use ResourceController\ServiceProvider;
use ResourceController\Tests\Traits\LoadTestConfiguration;

class TestCase extends SupportTestCase
{
    use LoadTestConfiguration;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $basePath = dirname(__DIR__);

        $this->loadMigrationsFrom($basePath.'/database/migrations');
        $this->withFactories($basePath.'/database/factories');
    }

    /**
     * @param  TestResponse  $response
     * @param  string  $file
     * @param  string  $function
     * @param  int  $statusCode
     */
    protected function assertFunctionSuccess(TestResponse $response, string $file, string $function, int $statusCode = 200)
    {
        $this->assertFunctionWithStatus($response, $file, $function, $statusCode);
    }

    /**
     * @param  TestResponse  $response
     * @param  string  $file
     * @param  string  $function
     * @param  int  $statusCode
     */
    protected function assertFunctionFailure(TestResponse $response, string $file, string $function, int $statusCode = 500)
    {
        $this->assertFunctionWithStatus($response, $file, $function, $statusCode);
    }

    /**
     * @param  TestResponse  $response
     * @param  string  $file
     * @param  string  $function
     * @param  int  $statusCode
     */
    protected function assertFunctionSuccessJson(TestResponse $response, string $file, string $function, int $statusCode = 200)
    {
        $this->assertFunctionWithStatus($response, $file, $function, $statusCode, true);
    }

    /**
     * @param  TestResponse  $response
     * @param  string  $file
     * @param  string  $function
     * @param  int  $statusCode
     */
    protected function assertFunctionFailureJson(TestResponse $response, string $file, string $function, int $statusCode = 500)
    {
        $this->assertFunctionWithStatus($response, $file, $function, $statusCode, true);
    }

    /**
     * @param  TestResponse  $response
     * @param  string  $file
     * @param  string  $function
     * @param  int  $statusCode
     * @param  bool  $json
     */
    private function assertFunctionWithStatus(TestResponse $response, string $file, string $function, int $statusCode = null, bool $json = false)
    {
        $filename = __DIR__.'/error-output/'.basename($file, '.php').'.'.trim($function, '.').($json ? '.json' : '.html');
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
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}
