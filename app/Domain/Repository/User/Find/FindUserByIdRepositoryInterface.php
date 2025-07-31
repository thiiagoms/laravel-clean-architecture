<?php

namespace App\Domain\Repository\User\Find;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\User;

interface FindUserByIdRepositoryInterface
{
    public function find(Id $id): ?User;
}
