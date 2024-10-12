<?php

declare(strict_types=1);

namespace App\DTO\User\Register;

use App\Contracts\DTO\User\Register\RegisterUserDTOContract;
use App\DTO\BaseDTO;
use App\Enums\User\UserRoleEnum;
use App\Http\Requests\User\Register\RegisterUserRequest;

class RegisterUserDTO extends BaseDTO implements RegisterUserDTOContract
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly UserRoleEnum $role = UserRoleEnum::USER,
    ) {}

    public static function fromRequest(RegisterUserRequest $request): RegisterUserDTO
    {
        return new self(...clean($request->validated()));
    }

    public static function fromArray(array $payload): RegisterUserDTO
    {
        return new self(...clean($payload));
    }
}
