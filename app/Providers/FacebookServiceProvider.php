<?php

namespace App\Providers;

use Facebook\Facebook;
use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Facebook::class, function ($app) {
            $config = config('services.facebook');
            // dd($config);
            return new Facebook([
                'app_id' => $config['app_id'],
                // 'app_id' => $config['client_id'],
                // 'app_secret' => $config['client_secret'],
                'app_secret' => $config['app_secret'],
                'default_graph_version' => 'v14.0',
            ]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
