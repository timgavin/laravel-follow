<?php

namespace TimGavin\LaravelFollow;

use Illuminate\Support\ServiceProvider;

class LaravelFollowServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-follow-migrations');

        $this->publishes([
            __DIR__.'/../config/laravel-follow.php' => config_path('laravel-follow.php'),
        ], 'laravel-follow-config');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-follow.php', 'laravel-follow');

        $this->app->singleton('laravel-follow', function ($app) {
            return new LaravelFollowManager;
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['laravel-follow'];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        //
    }
}
