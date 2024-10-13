<?php

declare(strict_types=1);

namespace App\Services\User\Find;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Services\User\Find\FindUserByEmailServiceContract;
use App\Contracts\Validators\Email\EmailValidatorContract;
use App\Models\User;

class FindUserByEmailService implements FindUserByEmailServiceContract
{
    public function __construct(
        private readonly EmailValidatorContract $emailValidator,
        private readonly UserRepositoryContract $userRepository
    ) {}

    public function handle(string $email): User|bool
    {
        $this->emailValidator->checkEmailIsValid($email);

        return $this->userRepository->findByEmail($email);
    }
}
