<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests;

use Illuminate\Foundation\Testing\TestResponse;
use Orchestra\Testbench\TestCase as SupportTestCase;
use Illuminate\Database\Eloquent\Relations\Relation;
use EricDowell\ResourceController\Tests\Models\TestPost;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use EricDowell\ResourceController\Tests\Console\TestKernel;
use EricDowell\ResourceController\Tests\Traits\LoadTestConfiguration;

class TestCase extends SupportTestCase
{
    use LoadTestConfiguration;

    /**
     * Output of Console
     *
     * @var string
     */
    protected $consoleOutput;

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $basePath = dirname(__DIR__);

        $this->loadMigrationsFrom($basePath.'/database/migrations');
        $this->withFactories($basePath.'/database/factories');

        Relation::morphMap([
            'post' => TestPost::class,
        ]);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->consoleOutput = '';
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
     * @param string $needle
     */
    protected function assertOutputContains($needle)
    {
        $this->assertContains($needle, $this->consoleOutput());
    }

    /**
     * @param string $needle
     */
    protected function assertOutputDoesNotContains($needle)
    {
        $this->assertNotContains($needle, $this->consoleOutput());
    }

    /**
     * Resolve application Console TestKernel implementation.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('artisan', function ($app) {
            return new \Illuminate\Console\Application($app, $app['events'], $app->version());
        });

        $app->singleton(ConsoleKernel::class, TestKernel::class);
    }

    /**
     * @param $command
     */
    protected function addCommand($command)
    {
        $this->app['Illuminate\Contracts\Console\Kernel']->registerCommand($command);
    }

    /**
     * Output of Console
     * @return mixed
     */
    public function consoleOutput()
    {
        return $this->consoleOutput ?: $this->consoleOutput = $this->app[ConsoleKernel::class]->output();
    }
}
