<?php

namespace App\Domain\Repository\User\Find;

use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;

interface FindUserByEmailRepositoryInterface
{
    public function find(Email $email): ?User;
}
