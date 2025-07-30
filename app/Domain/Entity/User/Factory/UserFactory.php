<?php

declare(strict_types=1);

namespace App\Domain\Entity\User\Factory;

use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;

abstract class UserFactory
{
    public static function create(Name $name, Email $email, Password $password): User
    {
        return new User(
            name: $name,
            email: $email,
            password: $password,
            role: Role::USER,
        );
    }
}
