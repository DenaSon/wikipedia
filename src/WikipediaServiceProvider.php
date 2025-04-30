<?php

namespace Denason\Wikipedia;

use Illuminate\Support\ServiceProvider;

class WikipediaServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->singleton(WikipediaInterface::class, function ($app) {
            return new Services\WikipediaManager();
        });


        //$this->mergeConfigFrom(__DIR__ . '/Config/wikipedia.php', 'wikipedia');
    }

    public function boot(): void
    {

        $this->publishes([
            __DIR__ . '/Config/wikipedia.php' => config_path('wikipedia.php'),
        ], 'wikipedia-config');


        if (file_exists(__DIR__ . '/Helpers.php')) {
            require_once __DIR__ . '/Helpers.php';
        }


    }
}
