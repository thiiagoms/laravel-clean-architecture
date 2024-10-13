<?php

declare(strict_types=1);

namespace App\Contracts\DTO\Auth\Authenticate;

use App\Contracts\DTO\BaseDTOContract;
use App\DTO\Auth\Authenticate\AuthenticateUserDTO;
use App\Http\Requests\Auth\AuthenticateUserRequest;

interface AuthenticateUserDTOContract extends BaseDTOContract
{
    public static function fromRequest(AuthenticateUserRequest $request): AuthenticateUserDTO;

    public static function fromArray(array $payload): AuthenticateUserDTO;
}
