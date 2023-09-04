<?php

namespace Melsaka\Metable;

use Illuminate\Support\ServiceProvider;

class MetableServiceProvider extends ServiceProvider
{
    // package migrations
    private $migration = __DIR__ . '/database/migrations/';

    private $config = __DIR__ . '/config/metable.php';


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->config, 'metable');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom([ $this->migration ]);

        $this->publishes([ $this->config => config_path('metable.php') ], 'metable');
    }
}
