<?php

namespace App\Infrastructure\Adapter\Service\Auth;

use App\Application\UseCases\Auth\Common\Interface\GenerateTokenInterface;
use App\Domain\Entity\Auth\Token\Factory\TokenFactory;
use App\Domain\Entity\Auth\Token\Token;
use App\Domain\Entity\User\User;
use App\Infrastructure\Persistence\Mapper\User\UserMapper;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class JWTTokenGeneratorService implements GenerateTokenInterface
{
    private const int SECONDS_PER_MINUTE = 60;

    private const string TOKEN_TYPE = 'Bearer';

    public function __construct(private AuthFactory $guard) {}

    public function create(User $user): Token
    {
        $guard = $this->guard->guard('api');

        $user = UserMapper::toModel($user);

        $token = $guard->fromUser($user);

        if (! $token) {
            throw new \RuntimeException('Failed to generate token');
        }

        $expiresIn = $guard->factory()->getTTL() * self::SECONDS_PER_MINUTE;

        return TokenFactory::create(token: $token, type: self::TOKEN_TYPE, expiresIn: $expiresIn);
    }
}
