<?php

namespace EricDowell\ResourceController\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as SupportTestCase;
use EricDowell\ResourceController\Tests\Traits\LoadTestConfiguration;

class TestCase extends SupportTestCase
{
    use LoadTestConfiguration;

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $basePath = dirname(__DIR__);

        $this->loadMigrationsFrom($basePath.'/database/migrations');

        Route::middleware('web')
            ->namespace('EricDowell\\ResourceController\\Tests\\Http\\Controllers')
            ->group($basePath.'/routes/web.php');
    }
}
