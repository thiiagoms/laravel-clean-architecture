<?php

declare(strict_types=1);

namespace App\Application\UseCases\User\Register\Service;

use App\Application\UseCases\User\Common\Validator\VerifyUserEmailIsAvailable;
use App\Domain\Entity\User\User;
use App\Domain\Repository\User\Register\RegisterUserRepositoryInterface;

readonly class RegisterUserService
{
    public function __construct(
        private VerifyUserEmailIsAvailable $guardAgainstEmailAlreadyInUse,
        private RegisterUserRepositoryInterface $repository
    ) {}

    public function create(User $user): User
    {
        $this->guardAgainstEmailAlreadyInUse->verify($user->getEmail());

        return $this->repository->save($user);
    }
}
