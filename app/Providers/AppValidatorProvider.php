<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppValidatorProvider extends ServiceProvider
{
    private array $validators = [];

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
