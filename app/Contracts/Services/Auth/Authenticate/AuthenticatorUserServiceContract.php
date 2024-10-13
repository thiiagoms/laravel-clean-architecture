<?php

declare(strict_types=1);

namespace App\Contracts\Services\Auth\Authenticate;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;

interface AuthenticatorUserServiceContract
{
    /**
     * @throws AuthenticationException
     */
    public function handle(User|bool $user, string $password): void;
}
