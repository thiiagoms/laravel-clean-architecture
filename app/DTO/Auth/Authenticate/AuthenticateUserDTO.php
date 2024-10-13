<?php

declare(strict_types=1);

namespace App\DTO\Auth\Authenticate;

use App\Contracts\DTO\Auth\Authenticate\AuthenticateUserDTOContract;
use App\DTO\BaseDTO;
use App\Http\Requests\Auth\AuthenticateUserRequest;

class AuthenticateUserDTO extends BaseDTO implements AuthenticateUserDTOContract
{
    public function __construct(public readonly string $email, public readonly string $password) {}

    public static function fromRequest(AuthenticateUserRequest $request): AuthenticateUserDTO
    {
        return new self(...clean($request->validated()));
    }

    public static function fromArray(array $payload): AuthenticateUserDTO
    {
        return new self(...clean($payload));
    }
}
