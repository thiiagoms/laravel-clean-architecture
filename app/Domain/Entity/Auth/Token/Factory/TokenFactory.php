<?php

declare(strict_types=1);

namespace App\Domain\Entity\Auth\Token\Factory;

use App\Domain\Entity\Auth\Token\Token;

abstract class TokenFactory
{
    public static function create(string $token, string $type, int $expiresIn): Token
    {
        return new Token(
            token: $token,
            type: $type,
            expiresIn: $expiresIn
        );
    }
}
