<?php

declare(strict_types=1);

namespace App\Application\UseCases\Task\Update\Service;

use App\Domain\Entity\Task\Task;
use App\Domain\Repository\Task\Update\UpdateTaskRepositoryInterface;

class UpdateTaskService
{
    public function __construct(private readonly UpdateTaskRepositoryInterface $repository) {}

    public function update(Task $task): Task
    {
        return $this->repository->update($task);
    }
}
