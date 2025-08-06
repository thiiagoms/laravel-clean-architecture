<?php

declare(strict_types=1);

namespace App\Application\UseCases\User\Common\Validator;

use App\Application\UseCases\User\Exception\EmailAlreadyExistsException;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Repository\User\Find\FindUserByEmailRepositoryInterface;

class VerifyUserEmailIsAvailable
{
    public function __construct(private readonly FindUserByEmailRepositoryInterface $repository) {}

    public function verify(Email $email): void
    {
        $user = $this->repository->find($email);

        if (! empty($user)) {
            throw EmailAlreadyExistsException::create();
        }
    }
}
