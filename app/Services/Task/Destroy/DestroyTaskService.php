<?php

declare(strict_types=1);

namespace App\Services\Task\Destroy;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Services\Task\Destroy\DestroyTaskServiceContract;
use App\Contracts\Services\Task\Find\FindTaskByIdServiceContract;

class DestroyTaskService implements DestroyTaskServiceContract
{
    public function __construct(
        private readonly FindTaskByIdServiceContract $findTaskByIdService,
        private readonly TaskRepositoryContract $taskRepository
    ) {}

    public function handle(string $id): bool
    {
        $this->findTaskByIdService->handle($id);

        return $this->taskRepository->destroy($id);
    }
}
