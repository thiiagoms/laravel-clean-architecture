<?php

declare(strict_types=1);

namespace App\Services\Task\Find;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Services\Task\Find\FindTasksByUserServiceContract;
use App\Contracts\Services\User\Find\FindUserByIdServiceContract;
use Illuminate\Database\Eloquent\Collection;

class FindTasksByUserService implements FindTasksByUserServiceContract
{
    public function __construct(
        private readonly FindUserByIdServiceContract $findUserByIdService,
        private readonly TaskRepositoryContract $taskRepository
    ) {}

    public function handle(string $userId): Collection
    {
        $this->findUserByIdService->handle($userId);

        return $this->taskRepository->findUserTasks($userId);
    }
}
