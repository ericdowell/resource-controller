<?php

namespace EricDowell\ResourceController;

use EricDowell\ResourceController\Console\Commands\RegisterUser;
use Illuminate\Support\ServiceProvider as SupportServiceProvider;

class ServiceProvider extends SupportServiceProvider
{
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
    }
}