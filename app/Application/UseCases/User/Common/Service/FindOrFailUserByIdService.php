<?php

declare(strict_types=1);

namespace App\Application\UseCases\User\Common\Service;

use App\Application\UseCases\User\Exception\UserNotFoundException;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\User;
use App\Domain\Repository\User\Find\FindUserByIdRepositoryInterface;

class FindOrFailUserByIdService
{
    public function __construct(private readonly FindUserByIdRepositoryInterface $repository) {}

    /**
     * @throws UserNotFoundException
     */
    public function findOrFail(Id $id): User
    {
        $user = $this->repository->find($id);

        if (empty($user)) {
            throw UserNotFoundException::create();
        }

        return $user;
    }
}
