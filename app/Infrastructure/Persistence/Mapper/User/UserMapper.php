<?php

namespace App\Infrastructure\Persistence\Mapper\User;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User as DomainUser;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Infrastructure\Persistence\Model\User as LaravelUserModel;

abstract class UserMapper
{
    public static function toDomain(LaravelUserModel $model): DomainUser
    {
        return new DomainUser(
            name: new Name($model->name),
            email: new Email($model->email),
            password: new Password(password: $model->password, hashed: false),
            role: Role::from($model->role),
            id: new Id($model->id),
            emailConfirmedAt: $model->email_verified_at
                ? $model->email_verified_at->toDateTimeImmutable()
                : null,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable()
        );
    }

    public static function toModel(DomainUser $user): LaravelUserModel
    {
        $model = new LaravelUserModel;

        $model->fill($user->toArray());

        return $model;
    }
}
