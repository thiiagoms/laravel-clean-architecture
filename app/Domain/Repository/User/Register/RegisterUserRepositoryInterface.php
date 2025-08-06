<?php

namespace App\Domain\Repository\User\Register;

use App\Domain\Entity\User\User;

interface RegisterUserRepositoryInterface
{
    public function save(User $user): User;
}
