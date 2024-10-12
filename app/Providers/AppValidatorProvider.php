<?php

namespace App\Providers;

use App\Contracts\Validators\Email\EmailValidatorContract;
use App\Contracts\Validators\User\UserEmailValidatorContract;
use App\Validators\Email\EmailValidator;
use App\Validators\User\UserEmailValidator;
use Illuminate\Support\ServiceProvider;

class AppValidatorProvider extends ServiceProvider
{
    private array $validators = [
        EmailValidatorContract::class => EmailValidator::class,
        /** begin: User */
        UserEmailValidatorContract::class => UserEmailValidator::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->validators as $contract => $validator) {
            $this->app->bind($contract, $validator);
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
