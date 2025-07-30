<?php

declare(strict_types=1);

namespace App\DTO\Auth\Authenticate;

use App\Contracts\DTO\Auth\Authenticate\AuthenticateUserDTOContract;
use App\DTO\BaseDTO;
use App\Http\Requests\Auth\AuthenticateUserRequest;
use App\Support\Sanitizer;

class AuthenticateUserDTO extends BaseDTO implements AuthenticateUserDTOContract
{
    public function __construct(public readonly string $email, public readonly string $password) {}

    public static function fromRequest(AuthenticateUserRequest $request): AuthenticateUserDTO
    {
        $data = Sanitizer::clean($request->validated());

        return new self(...$data);
    }

    public static function fromArray(array $payload): AuthenticateUserDTO
    {
        $data = Sanitizer::clean($payload);

        return new self(...$data);
    }
}
