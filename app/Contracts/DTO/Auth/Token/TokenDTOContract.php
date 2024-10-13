<?php

declare(strict_types=1);

namespace App\Contracts\DTO\Auth\Token;

use App\Contracts\DTO\BaseDTOContract;
use App\DTO\Auth\Token\TokenDTO;

interface TokenDTOContract extends BaseDTOContract
{
    public static function fromArray(array $payload): TokenDTO;
}
