<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CyberbullyingNaiveBayes;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(CyberbullyingNaiveBayes::class, function ($app) {
            return new CyberbullyingNaiveBayes();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
