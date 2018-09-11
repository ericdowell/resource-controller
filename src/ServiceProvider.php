<?php

namespace EricDowell\ResourceController;

use EricDowell\ResourceController\Commands\RegisterUser;
use Illuminate\Support\ServiceProvider as SupportServiceProvider;

class ServiceProvider extends SupportServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->packageConfigFilePath() => config_path($this->packageConfigFilename()),
        ], $this->packageName());

        $this->loadViewsFrom($this->packageBasePath('views'), $this->packageName());
    }

    /**
     * Register Resource Controller services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            RegisterUser::class,
        ]);

        $this->mergeConfigFrom($this->packageConfigFilePath(), $this->packageName());
    }

    /**
     * Name of the package.
     *
     * @return string
     */
    protected function packageName()
    {
        return 'resource-controller';
    }

    /**
     * Return the base path for this package.
     *
     * @param string $path
     *
     * @return string
     */
    protected function packageBasePath(string $path)
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.$path;
    }

    /**
     * Filename of config for package.
     *
     * @return string
     */
    protected function packageConfigFilename()
    {
        return $this->packageName().'.php';
    }

    /**
     * File path of config for package.
     *
     * @return string
     */
    protected function packageConfigFilePath()
    {
        return $this->packageBasePath('config'.DIRECTORY_SEPARATOR.$this->packageConfigFilename());
    }
}
