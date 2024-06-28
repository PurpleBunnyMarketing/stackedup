<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Http\ViewComposers\AdminComposer::class);
        $this->app->singleton(\App\Http\ViewComposers\LoginComposer::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['admin.auth.*', 'frontend.pages.general.index', 'frontend.pages.general.about-us-without-login', 'frontend.pages.general.contact-us'], 'App\Http\ViewComposers\LoginComposer');
        view()->composer(['admin.pages.*', 'admin.layouts.*', 'layouts.*'], 'App\Http\ViewComposers\AdminComposer');
    }
}
