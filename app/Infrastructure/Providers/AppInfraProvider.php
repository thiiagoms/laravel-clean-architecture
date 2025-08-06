<?php

namespace App\Infrastructure\Providers;

use App\Application\UseCases\Auth\Common\Interface\GenerateTokenInterface;
use App\Infrastructure\Adapter\Service\Auth\JWTTokenGeneratorService;
use Illuminate\Support\ServiceProvider;

class AppInfraProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerInfrastructureServices();
    }

    private function registerInfrastructureServices(): void
    {
        $this->app->bind(
            abstract: GenerateTokenInterface::class,
            concrete: JWTTokenGeneratorService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
