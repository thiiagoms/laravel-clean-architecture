<?php

declare(strict_types=1);

namespace App\Services\Auth\Authenticate;

use App\Contracts\Services\Auth\Authenticate\AuthenticatorUserServiceContract;
use App\Contracts\Validators\Auth\HashValidatorContract;
use App\Messages\Auth\AuthMessage;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;

class AuthenticatorUserService implements AuthenticatorUserServiceContract
{
    public function __construct(private readonly HashValidatorContract $hashValidator) {}

    public function handle(User|bool $user, string $password): void
    {
        $credentialsAreValid =
            $user !== false &&
            $this->hashValidator->checkPasswordHashMatch($password, $user->password);

        throw_if(! $credentialsAreValid, new AuthenticationException(AuthMessage::INVALID_CREDENTIALS));
    }
}
