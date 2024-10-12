<?php

declare(strict_types=1);

namespace App\Services\User\Register;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Services\User\Register\RegisterUserServiceContract;
use App\Contracts\Validators\User\UserEmailValidatorContract;
use App\DTO\User\Register\RegisterUserDTO;
use App\Models\User;

class RegisterUserService implements RegisterUserServiceContract
{
    public function __construct(
        private readonly UserEmailValidatorContract $userEmailValidator,
        private readonly UserRepositoryContract $userRepository
    ) {}

    public function handle(RegisterUserDTO $registerUserDTO): User
    {
        $this->userEmailValidator->checkUserEmailIsAvailable($registerUserDTO->email);

        return $this->userRepository->create($registerUserDTO->toArray());
    }
}
