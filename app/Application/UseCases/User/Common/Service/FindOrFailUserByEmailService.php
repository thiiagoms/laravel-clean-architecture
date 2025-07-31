<?php

declare(strict_types=1);

namespace App\Application\UseCases\User\Common\Service;

use App\Application\UseCases\User\Exception\UserNotFoundException;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Repository\User\Find\FindUserByEmailRepositoryInterface;

class FindOrFailUserByEmailService
{
    public function __construct(private readonly FindUserByEmailRepositoryInterface $repository) {}

    public function findOrFail(Email $email): User
    {
        $user = $this->repository->find($email);

        if (empty($user)) {
            throw UserNotFoundException::create();
        }

        return $user;
    }
}
