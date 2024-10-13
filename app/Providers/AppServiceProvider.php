<?php

namespace App\Providers;

use App\Contracts\Services\Auth\Authenticate\AuthenticateUserServiceContract;
use App\Contracts\Services\Auth\Authenticate\AuthenticatorUserServiceContract;
use App\Contracts\Services\Auth\Token\TokenExceptionHandlerContract;
use App\Contracts\Services\Auth\Token\TokenGeneratorServiceContract;
use App\Contracts\Services\Task\Register\RegisterTaskServiceContract;
use App\Contracts\Services\User\Find\FindUserByEmailServiceContract;
use App\Contracts\Services\User\Find\FindUserByIdServiceContract;
use App\Contracts\Services\User\Register\RegisterUserServiceContract;
use App\Services\Auth\Authenticate\AuthenticateUserService;
use App\Services\Auth\Authenticate\AuthenticatorUserService;
use App\Services\Auth\Token\TokenExceptionHandler;
use App\Services\Auth\Token\TokenGeneratorService;
use App\Services\Task\Register\RegisterTaskService;
use App\Services\User\Find\FindUserByEmailService;
use App\Services\User\Find\FindUserByIdService;
use App\Services\User\Register\RegisterUserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private array $services = [
        /** begin: User */
        RegisterUserServiceContract::class => RegisterUserService::class,
        FindUserByEmailServiceContract::class => FindUserByEmailService::class,
        FindUserByIdServiceContract::class => FindUserByIdService::class,
        /** begin: Auth */
        AuthenticateUserServiceContract::class => AuthenticateUserService::class,
        AuthenticatorUserServiceContract::class => AuthenticatorUserService::class,
        TokenGeneratorServiceContract::class => TokenGeneratorService::class,
        TokenExceptionHandlerContract::class => TokenExceptionHandler::class,
        /** begin: Task */
        RegisterTaskServiceContract::class => RegisterTaskService::class,
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
