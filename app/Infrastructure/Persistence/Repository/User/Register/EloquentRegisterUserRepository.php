<?php

namespace App\Infrastructure\Persistence\Repository\User\Register;

use App\Domain\Entity\User\User;
use App\Domain\Repository\User\Register\RegisterUserRepositoryInterface;
use App\Infrastructure\Persistence\Mapper\User\UserMapper;
use App\Infrastructure\Persistence\Repository\User\BaseUserRepository;

final class EloquentRegisterUserRepository extends BaseUserRepository implements RegisterUserRepositoryInterface
{
    public function save(User $user): User
    {
        $user = $this->model->create($user->toArray());

        return UserMapper::toDomain($user);
    }
}
