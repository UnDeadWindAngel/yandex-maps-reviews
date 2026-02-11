<?php

namespace App\Providers;

use App\Services\YandexMapsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(YandexMapsService::class, function ($app) {
            return new YandexMapsService();
        });
    }

    public function boot(): void
    {
        //
    }
}
