<?php

namespace App\Domain\Repository\Task\Find;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Task;

interface FindTaskByIdRepositoryInterface
{
    public function find(Id $id): ?Task;
}
