<?php

namespace EricDowell\ResourceController\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use EricDowell\ResourceController\Tests\Models\TestPost;

class TestServiceProvider extends ServiceProvider
{
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

        Route::middleware('web')
            ->namespace('EricDowell\\ResourceController\\Tests\\Http\\Controllers')
            ->group(dirname(__DIR__).'/routes/web.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
