<?php

namespace Devinweb\LaravelYoucanPay\Providers;

use Devinweb\LaravelYoucanPay\LaravelYoucanPay;
use Illuminate\Support\ServiceProvider;

class LaravelYoucanPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-youcan-pay');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-youcan-pay');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/youcanpay.php' => config_path('youcanpay.php'),
            ], 'youcanpay-config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-youcan-pay'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-youcan-pay'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-youcan-pay'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../../config/youcanpay.php', 'youcanpay');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-youcan-pay', function () {
            return new LaravelYoucanPay;
        });
    }
}
