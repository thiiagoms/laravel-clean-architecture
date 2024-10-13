<?php

declare(strict_types=1);

namespace App\Services\Auth\Token;

use App\Contracts\Services\Auth\Token\TokenExceptionHandlerContract;
use App\Messages\Auth\AuthMessage;
use Illuminate\Auth\AuthenticationException;

class TokenExceptionHandler implements TokenExceptionHandlerContract
{
    /**
     * @throws AuthenticationException
     */
    public function handle(string|bool $token): string
    {
        return match (true) {
            $token !== false => $token,
            default => throw new AuthenticationException(AuthMessage::INVALID_CREDENTIALS),
        };
    }
}
