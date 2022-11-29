<?php

namespace Devinweb\LaravelYouCanPay\Providers;

use Devinweb\LaravelYouCanPay\Console\CleanPendingTransactionCommand;
use Devinweb\LaravelYouCanPay\Http\Middleware\VerifyWebhookSignature;
use Devinweb\LaravelYouCanPay\LaravelYouCanPay;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

class LaravelYouCanPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        $this->registerMiddleware();
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerPublishing();
        $this->registerCommands();
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        }
    }


        /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                 __DIR__.'/../../config/youcanpay.php' => config_path('youcanpay.php'),
             ], 'youcanpay-config');


            $this->publishes([
                __DIR__.'/../../database/migrations' => $this->app->databasePath('migrations'),
            ], 'youcanpay-migrations');
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
            return new LaravelYouCanPay;
        });
    }


    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => 'youcanpay',
            'namespace' => 'Devinweb\LaravelYouCanPay\Http\Controllers',
            'as' => 'youcanpay.',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        });
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function registerMiddleware()
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('verify-youcanpay-webhook-signature', VerifyWebhookSignature::class);
    }

        /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanPendingTransactionCommand::class,
            ]);
        }
    }
}
