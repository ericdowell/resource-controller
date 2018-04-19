<?php

namespace EricDowell\ResourceController\Tests;

use EricDowell\ResourceController\ServiceProvider;
use Orchestra\Testbench\TestCase as SupportTestCase;
use EricDowell\ResourceController\Tests\Traits\LoadTestConfiguration;

class TestCase extends SupportTestCase
{
    use LoadTestConfiguration;

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
