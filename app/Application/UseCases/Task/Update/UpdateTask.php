<?php

declare(strict_types=1);

namespace App\Application\UseCases\Task\Update;

use App\Application\UseCases\Task\Common\Service\FindOrFailTaskByIdService;
use App\Application\UseCases\Task\Update\DTO\UpdateTaskDTO;
use App\Application\UseCases\Task\Update\Service\TaskEntityUpdater;
use App\Application\UseCases\Task\Update\Service\UpdateTaskService;
use App\Domain\Entity\Task\Task;

final class UpdateTask
{
    public function __construct(
        private readonly FindOrFailTaskByIdService $findOrFailTaskByIdService,
        private readonly UpdateTaskService $service
    ) {}

    public function handle(UpdateTaskDTO $dto): Task
    {
        $task = $this->findOrFailTaskByIdService->findOrFail($dto->getId());

        $task = TaskEntityUpdater::mapper(task: $task, dto: $dto);

        return $this->service->update($task);
    }
}
