<?php

declare(strict_types=1);

namespace App\Contracts\Services\Auth\Authenticate;

use App\DTO\Auth\Authenticate\AuthenticateUserDTO;
use App\DTO\Auth\Token\TokenDTO;

interface AuthenticateUserServiceContract
{
    public function handle(AuthenticateUserDTO $authenticateUserDTO): TokenDTO;
}
