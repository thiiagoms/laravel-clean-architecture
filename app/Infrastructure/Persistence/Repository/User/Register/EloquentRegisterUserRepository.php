<?php

namespace App\Infrastructure\Persistence\Repository\User\Register;

use App\Domain\Entity\User\User;
use App\Domain\Repository\User\Register\RegisterUserRepositoryInterface;
use App\Infrastructure\Adapter\UserAdapter;
use App\Infrastructure\Persistence\Repository\User\BaseUserRepository;

final class EloquentRegisterUserRepository extends BaseUserRepository implements RegisterUserRepositoryInterface
{
    public function save(User $user): User
    {
        $user = $this->model->create($user->toArray());

        return UserAdapter::toDomain($user);
    }
}
