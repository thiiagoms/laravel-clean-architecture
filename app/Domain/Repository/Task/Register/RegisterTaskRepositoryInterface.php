<?php

namespace App\Domain\Repository\Task\Register;

use App\Domain\Entity\Task\Task;

interface RegisterTaskRepositoryInterface
{
    public function save(Task $task): Task;
}
