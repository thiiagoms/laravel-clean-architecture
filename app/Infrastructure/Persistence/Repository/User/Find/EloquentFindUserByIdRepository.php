<?php

namespace App\Infrastructure\Persistence\Repository\User\Find;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\User;
use App\Domain\Repository\User\Find\FindUserByIdRepositoryInterface;
use App\Infrastructure\Adapter\UserAdapter;
use App\Infrastructure\Persistence\Repository\User\BaseUserRepository;

final class EloquentFindUserByIdRepository extends BaseUserRepository implements FindUserByIdRepositoryInterface
{
    public function find(Id $id): ?User
    {
        $user = $this->model->find($id->getValue());

        return empty($user) ? null : UserAdapter::toDomain($user);
    }
}
