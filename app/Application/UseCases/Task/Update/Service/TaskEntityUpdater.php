<?php

namespace App\Application\UseCases\Task\Update\Service;

use App\Application\UseCases\Task\Update\DTO\UpdateTaskDTO;
use App\Domain\Entity\Task\Status\Factory\StatusFactory;
use App\Domain\Entity\Task\Task;

class TaskEntityUpdater
{
    public static function mapper(Task $task, UpdateTaskDTO $dto): Task
    {
        return new Task(
            title: $dto->getTitle() ?? $task->getTitle(),
            description: $dto->getDescription() ?? $task->getDescription(),
            owner: $task->getOwner(),
            status: empty($dto->getStatus())
                ? StatusFactory::map($task->getStatus())
                : StatusFactory::map($dto->getStatus()),
            id: $task->getId(),
            createdAt: $task->getCreatedAt(),
            updatedAt: new \DateTimeImmutable,
        );
    }
}
