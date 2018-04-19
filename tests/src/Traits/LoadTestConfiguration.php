<?php

namespace EricDowell\ResourceController\Tests\Traits;

use SplFileInfo;
use Symfony\Component\Finder\Finder;

trait LoadTestConfiguration
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->loadTestConfigs($app);
    }

    /**
     * Load config from one folder up.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function loadTestConfigs($app)
    {
        foreach ($this->getConfigurationFiles() as $key => $path) {
            $config = require $path;

            if ($app['config']->has($key)) {
                $config = array_replace_recursive($app['config']->get($key), $config);
            }

            $app['config']->set($key, $config);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @return array
     */
    protected function getConfigurationFiles()
    {
        $files = [];

        $configPath = realpath(dirname(dirname(__DIR__))).'/config/';

        /** @var SplFileInfo $file */
        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }
}
