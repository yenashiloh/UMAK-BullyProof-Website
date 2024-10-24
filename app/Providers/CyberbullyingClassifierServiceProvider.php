<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CyberbullyingClassifier;

class CyberbullyingClassifierServiceProvider extends ServiceProvider
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
