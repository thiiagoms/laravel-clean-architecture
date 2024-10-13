<?php

declare(strict_types=1);

namespace App\Services\Task\Register;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Services\Task\Register\RegisterTaskServiceContract;
use App\Contracts\Services\User\Find\FindUserByIdServiceContract;
use App\DTO\Task\Register\RegisterTaskDTO;
use App\Models\Task;

class RegisterTaskService implements RegisterTaskServiceContract
{
    public function __construct(
        private readonly FindUserByIdServiceContract $findUserByIdService,
        private readonly TaskRepositoryContract $taskRepository
    ) {}

    public function handle(RegisterTaskDTO $registerTaskDTO): Task
    {
        $this->findUserByIdService->handle($registerTaskDTO->user_id);

        return $this->taskRepository->create($registerTaskDTO->toArray());
    }
}
