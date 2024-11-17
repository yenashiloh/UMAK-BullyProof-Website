<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PythonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(CyberbullyingClassifier::class, function ($app) {
            return new CyberbullyingClassifier();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
