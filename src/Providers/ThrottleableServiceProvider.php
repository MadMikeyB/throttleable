<?php

namespace MadMikeyB\Throttleable\Providers;

use Illuminate\Support\ServiceProvider;

class ThrottleableServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (! class_exists(\CreateThrottlesTable::class)) {
            $timestamp = date('Y_m_d_His');

            $this->publishes([
                __DIR__ . '/../../database/migrations/create_throttles_table.php.stub' => database_path("/migrations/{$timestamp}_create_throttles_table.php"),
            ], 'migrations');
        }

        $this->publishes([
           __DIR__ . '/../../config/throttleable.php' => config_path('throttleable.php'),
        ], 'config');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/throttleable.php',
            'throttleable'
        );
    }
}
