<?php

namespace Devitek\Laravel\Eloquent\Loggable;

use Illuminate\Support\ServiceProvider;

class EloquentLoggableServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Nothing to register in this package
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ], 'migrations');
    }
}
