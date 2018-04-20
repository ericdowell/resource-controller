<?php

namespace EricDowell\ResourceController\Tests;

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
        $this->withFactories($basePath.'/database/factories');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [TestServiceProvider::class];
    }
}
