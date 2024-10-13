<?php

declare(strict_types=1);

namespace App\Services\Auth\Token;

use App\Contracts\Services\Auth\Token\TokenExceptionHandlerContract;
use App\Contracts\Services\Auth\Token\TokenGeneratorServiceContract;
use App\DTO\Auth\Authenticate\AuthenticateUserDTO;
use App\DTO\Auth\Token\TokenDTO;

class TokenGeneratorService implements TokenGeneratorServiceContract
{
    public function __construct(private readonly TokenExceptionHandlerContract $tokenExceptionHandler) {}

    public function generateToken(AuthenticateUserDTO $authDTO): string
    {
        $token = auth('api')->attempt($authDTO->toArray());

        return $this->tokenExceptionHandler->handle($token);
    }

    public function responseWithToken(string $token): TokenDTO
    {
        $expiresIn = auth('api')->factory()->getTTL() * config('jwt.ttl');

        return TokenDTO::fromArray(['token' => $token, 'type' => 'bearer', 'expires_in' => $expiresIn]);
    }
}
