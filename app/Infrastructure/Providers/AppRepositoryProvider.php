<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repository\Task\Destroy\DestroyTaskRepositoryInterface;
use App\Domain\Repository\Task\Find\FindTaskByIdRepositoryInterface;
use App\Domain\Repository\Task\Register\RegisterTaskRepositoryInterface;
use App\Domain\Repository\Task\Update\UpdateTaskRepositoryInterface;
use App\Domain\Repository\User\Find\FindUserByEmailRepositoryInterface;
use App\Domain\Repository\User\Find\FindUserByIdRepositoryInterface;
use App\Domain\Repository\User\Register\RegisterUserRepositoryInterface;
use App\Infrastructure\Persistence\Repository\Task\Destroy\EloquentDestroyTaskByIdRepository;
use App\Infrastructure\Persistence\Repository\Task\Find\EloquentFindTaskByIdRepository;
use App\Infrastructure\Persistence\Repository\Task\Register\EloquentRegisterTaskRepository;
use App\Infrastructure\Persistence\Repository\Task\Update\EloquentUpdateTaskRepository;
use App\Infrastructure\Persistence\Repository\User\Find\EloquentFindUserByEmailRepository;
use App\Infrastructure\Persistence\Repository\User\Find\EloquentFindUserByIdRepository;
use App\Infrastructure\Persistence\Repository\User\Register\EloquentRegisterUserRepository;
use Illuminate\Support\ServiceProvider;

class AppRepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerUserRepositories();
        $this->registerTaskREpositories();
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

    private function registerTaskRepositories(): void
    {
        $this->app->bind(
            abstract: RegisterTaskRepositoryInterface::class,
            concrete: EloquentRegisterTaskRepository::class
        );

        $this->app->bind(
            abstract: FindTaskByIdRepositoryInterface::class,
            concrete: EloquentFindTaskByIdRepository::class
        );

        $this->app->bind(
            abstract: UpdateTaskRepositoryInterface::class,
            concrete: EloquentUpdateTaskRepository::class
        );

        $this->app->bind(
            abstract: DestroyTaskRepositoryInterface::class,
            concrete: EloquentDestroyTaskByIdRepository::class
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
