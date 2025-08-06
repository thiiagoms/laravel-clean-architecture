<?php

namespace App\Domain\Repository\Task\Update;

use App\Domain\Entity\Task\Task;

interface UpdateTaskRepositoryInterface
{
    public function update(Task $task): Task;
}
