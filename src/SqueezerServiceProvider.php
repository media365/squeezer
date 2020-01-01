<?php

namespace Media365\Squeezer;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class SqueezerServiceProvider extends ServiceProvider
{
    /**
     * Boot package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/squeezer.php', 'squeezer');
    }

    /**
     * Register package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('url.shortener', SqueezerManager::class);
        $this->app->bindIf(ClientInterface::class, Client::class);

        $this->app->singleton('url.shortener', function ($app) {
            return new SqueezerManager($app);
        });
    }
}
