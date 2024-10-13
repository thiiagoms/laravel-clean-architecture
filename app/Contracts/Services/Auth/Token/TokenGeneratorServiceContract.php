<?php

declare(strict_types=1);

namespace App\Contracts\Services\Auth\Token;

use App\DTO\Auth\Authenticate\AuthenticateUserDTO;
use App\DTO\Auth\Token\TokenDTO;

interface TokenGeneratorServiceContract
{
    public function generateToken(AuthenticateUserDTO $authDTO): string;

    public function responseWithToken(string $token): TokenDTO;
}
