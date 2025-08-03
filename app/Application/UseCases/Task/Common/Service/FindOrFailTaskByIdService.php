<?php

declare(strict_types=1);

namespace App\Application\UseCases\Task\Common\Service;

use App\Application\UseCases\Task\Exception\TaskNotFoundException;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Task;
use App\Domain\Repository\Task\Find\FindTaskByIdRepositoryInterface;

class FindOrFailTaskByIdService
{
    public function __construct(private readonly FindTaskByIdRepositoryInterface $repository) {}

    public function findOrFail(Id $id): Task
    {
        $task = $this->repository->find($id);

        if (empty($task)) {
            throw TaskNotFoundException::create();
        }

        return $task;
    }
}
