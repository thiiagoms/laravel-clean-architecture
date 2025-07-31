<?php

namespace App\Infrastructure\Providers;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Domain\Repository\User\Find\FindUserByEmailRepositoryInterface;
use App\Domain\Repository\User\Find\FindUserByIdRepositoryInterface;
use App\Domain\Repository\User\Register\RegisterUserRepositoryInterface;
use App\Infrastructure\Persistence\Repository\User\Find\EloquentFindUserByEmailRepository;
use App\Infrastructure\Persistence\Repository\User\Find\EloquentFindUserByIdRepository;
use App\Infrastructure\Persistence\Repository\User\Register\EloquentRegisterUserRepository;
use App\Repositories\Task\TaskRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppRepositoryProvider extends ServiceProvider
{
    private array $repositories = [
        UserRepositoryContract::class => UserRepository::class,
        TaskRepositoryContract::class => TaskRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerUserRepositories();

        foreach ($this->repositories as $contract => $repository) {
            $this->app->bind($contract, $repository);
        }
    }

    private function registerUserRepositories(): void
    {
        $this->app->bind(
            abstract: FindUserByEmailRepositoryInterface::class,
            concrete: EloquentFindUserByEmailRepository::class
        );

        $this->app->bind(
            abstract: FindUserByIdRepositoryInterface::class,
            concrete: EloquentFindUserByIdRepository::class
        );

        $this->app->bind(
            abstract: RegisterUserRepositoryInterface::class,
            concrete: EloquentRegisterUserRepository::class
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
