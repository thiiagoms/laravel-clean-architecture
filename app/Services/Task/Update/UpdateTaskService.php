<?php

declare(strict_types=1);

namespace App\Services\Task\Update;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Services\Task\Find\FindTaskByIdServiceContract;
use App\Contracts\Services\Task\Update\UpdateTaskServiceContract;
use App\Contracts\Services\User\Find\FindUserByIdServiceContract;
use App\DTO\Task\Update\UpdateTaskDTO;
use App\Models\Task;

class UpdateTaskService implements UpdateTaskServiceContract
{
    public function __construct(
        private readonly FindTaskByIdServiceContract $findTaskByIdService,
        private readonly FindUserByIdServiceContract $findUserByIdService,
        private readonly TaskRepositoryContract $taskRepository
    ) {}

    public function handle(UpdateTaskDTO $updateTaskDTO): Task
    {
        $this->findTaskByIdService->handle($updateTaskDTO->id);

        $this->findUserByIdService->handle($updateTaskDTO->user_id);

        $taskData = removeEmpty($updateTaskDTO->toArray());

        $this->taskRepository->update($updateTaskDTO->id, $taskData);

        return $this->findTaskByIdService->handle($updateTaskDTO->id);
    }
}
