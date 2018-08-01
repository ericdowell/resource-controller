<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Console;

use Exception;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class TestKernel extends ConsoleKernel
{
    /**
     * The bootstrap classes for the application.
     *
     * @return void
     */
    protected $bootstrappers = [];

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Exception $exception
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function reportException(Exception $exception)
    {
        throw $exception;
    }

    /**
     * Get the Artisan application instance.
     *
     * @return \Illuminate\Console\Application
     */
    public function getArtisan()
    {
        return $this->app['artisan'];
    }
}
