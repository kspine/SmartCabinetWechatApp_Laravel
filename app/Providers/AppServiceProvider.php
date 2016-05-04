<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    //protected $defer = true;
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     * You should only bind things into the service container
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
//    public function provides()
//    {
//        return [Wechatter::class];
//    }
}
