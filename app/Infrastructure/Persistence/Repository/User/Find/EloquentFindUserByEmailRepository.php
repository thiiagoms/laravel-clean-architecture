<?php

namespace App\Infrastructure\Persistence\Repository\User\Find;

use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Repository\User\Find\FindUserByEmailRepositoryInterface;
use App\Infrastructure\Persistence\Mapper\User\UserMapper;
use App\Infrastructure\Persistence\Repository\User\BaseUserRepository;

final class EloquentFindUserByEmailRepository extends BaseUserRepository implements FindUserByEmailRepositoryInterface
{
    public function find(Email $email): ?User
    {
        $user = $this->model->where('email', $email->getValue())->first();

        return empty($user) ? null : UserMapper::toDomain($user);
    }
}
