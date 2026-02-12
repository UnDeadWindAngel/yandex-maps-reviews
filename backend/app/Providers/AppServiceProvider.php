<?php

namespace App\Providers;

use App\Contracts\YandexMapsServiceInterface;
use App\Services\FakeYandexMapsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(YandexMapsServiceInterface::class, function ($app) {
            $driver = config('services.yandex.driver', 'fake');

            return match ($driver) {
                // Здесь будут другие драйверы по мере появления
                default => new FakeYandexMapsService(),
            };
        });
    }

    public function boot(): void
    {
        //
    }
}
