<?php

namespace Dwoodard\Sketch;

use Illuminate\Support\ServiceProvider;

class SketchServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'dwoodard');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'dwoodard');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sketch.php', 'sketch');

        // Register the service the package provides.
        $this->app->singleton('sketch', function ($app) {
            return new Sketch;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sketch'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/sketch.php' => config_path('sketch.php'),
        ], 'sketch.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/dwoodard'),
        ], 'sketch.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/dwoodard'),
        ], 'sketch.views');*/

        // Registering package commands.
         $this->commands([
             \Dwoodard\Sketch\Console\Commands\SketchGenerate::class,
             \Dwoodard\Sketch\Console\Commands\SketchInit::class,
         ]);
    }
}
