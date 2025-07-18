<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Translation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // for Performance Optimization
        Translation::preventLazyLoading(!app()->isProduction());
    }
}
