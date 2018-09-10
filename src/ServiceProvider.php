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
        $basePath = dirname(__DIR__);

        $this->publishes([
            $basePath.'/config/resource-controller.php' => config_path('resource-controller.php'),
        ], 'resource-controller');

        $this->loadViewsFrom($basePath.'/views', 'resource-controller');
    }

    /**
     * Register Resource Controller services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/resource-controller.php', 'resource-controller');

        $this->commands([
            RegisterUser::class,
        ]);
    }
}
