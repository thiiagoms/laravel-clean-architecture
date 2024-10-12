<?php

namespace App\Providers;

use App\Contracts\Services\User\Register\RegisterUserServiceContract;
use App\Services\User\Register\RegisterUserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private array $services = [
        RegisterUserServiceContract::class => RegisterUserService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->services as $contract => $service) {
            $this->app->bind($contract, $service);
        }
    }

    private function loadMigrationsFromDirectories(): void
    {
        $mainPath = database_path('migrations');
        $directories = glob($mainPath.'/*', GLOB_ONLYDIR);
        $paths = array_merge([$mainPath], $directories);

        $this->loadMigrationsFrom($paths);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFromDirectories();
    }
}
