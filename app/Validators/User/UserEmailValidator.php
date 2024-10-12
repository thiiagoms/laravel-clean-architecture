<?php

declare(strict_types=1);

namespace App\Validators\User;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Validators\Email\EmailValidatorContract;
use App\Contracts\Validators\User\UserEmailValidatorContract;
use App\Messages\User\UserEmailMessage;
use App\Models\User;
use DomainException;

class UserEmailValidator implements UserEmailValidatorContract
{
    public function __construct(
        private readonly EmailValidatorContract $emailValidator,
        private readonly UserRepositoryContract $userRepository
    ) {}

    /**
     * @throws DomainException
     */
    private function checkEmailExists(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        $this->assertEmailIsUnique($user);
    }

    /**
     * @throws DomainException
     */
    private function assertEmailIsUnique(User|bool $user): void
    {
        throw_if($user, new DomainException(UserEmailMessage::emailAlreadyExists()));
    }

    /**
     * @throws DomainException
     * @throws LogicalException
     */
    public function checkUserEmailIsAvailable(string $email): void
    {
        $this->emailValidator->checkEmailIsValid($email);

        $this->checkEmailExists($email);
    }
}
