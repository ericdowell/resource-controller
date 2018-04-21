<?php

namespace EricDowell\ResourceController\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Relations\Relation;
use EricDowell\ResourceController\Tests\Models\TestPost;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'EricDowell\ResourceController\Tests\Http\Controllers';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'post' => TestPost::class,
        ]);

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(dirname(__DIR__).'/routes/web.php');
    }
}
