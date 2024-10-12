<?php

namespace App\Providers;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppRepositoryProvider extends ServiceProvider
{
    private array $repositories = [
        UserRepositoryContract::class => UserRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->repositories as $contract => $repository) {
            $this->app->bind($contract, $repository);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
