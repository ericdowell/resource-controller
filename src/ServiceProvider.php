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
            $this->basePath("config/{$this->packageConfig()}") => config_path($this->packageConfig()),
        ], $this->packageName());

        $this->loadViewsFrom($this->basePath('/views'), $this->packageName());
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

        $this->mergeConfigFrom($this->basePath("config/{$this->packageConfig()}"), $this->packageName());
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
     * Filename of config for package.
     *
     * @return string
     */
    protected function packageConfig()
    {
        return "{$this->packageName()}.php";
    }

    /**
     * Return the base path for this package.
     *
     * @return string
     */
    protected function basePath(string $path = null)
    {
        return dirname(__DIR__).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
