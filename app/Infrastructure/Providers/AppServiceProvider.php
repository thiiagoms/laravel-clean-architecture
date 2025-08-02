<?php

namespace App\Infrastructure\Providers;

use App\Contracts\Services\Task\Destroy\DestroyTaskServiceContract;
use App\Contracts\Services\Task\Find\FindTaskByIdServiceContract;
use App\Contracts\Services\Task\Find\FindTasksByUserServiceContract;
use App\Contracts\Services\Task\Register\RegisterTaskServiceContract;
use App\Contracts\Services\Task\Update\UpdateTaskServiceContract;
use App\Services\Task\Destroy\DestroyTaskService;
use App\Services\Task\Find\FindTaskByIdService;
use App\Services\Task\Find\FindTasksByUserService;
use App\Services\Task\Register\RegisterTaskService;
use App\Services\Task\Update\UpdateTaskService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private array $services = [
        /** begin: Task */
        RegisterTaskServiceContract::class => RegisterTaskService::class,
        FindTaskByIdServiceContract::class => FindTaskByIdService::class,
        UpdateTaskServiceContract::class => UpdateTaskService::class,
        DestroyTaskServiceContract::class => DestroyTaskService::class,
        FindTasksByUserServiceContract::class => FindTasksByUserService::class,
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
