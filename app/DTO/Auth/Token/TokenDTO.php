<?php

declare(strict_types=1);

namespace App\DTO\Auth\Token;

use App\Contracts\DTO\Auth\Token\TokenDTOContract;
use App\DTO\BaseDTO;

class TokenDTO extends BaseDTO implements TokenDTOContract
{
    public function __construct(
        public readonly string $token,
        public readonly string $type,
        public readonly int $expires_in
    ) {}

    public static function fromArray(array $payload): TokenDTO
    {
        return new self(...$payload);
    }
}
