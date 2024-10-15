<?php

declare(strict_types=1);

namespace App\Services\Task\Find;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Services\Task\Find\FindTaskByIdServiceContract;
use App\Contracts\Validators\Uuid\UuidValidatorContract;
use App\Messages\System\SystemMessage;
use App\Models\Task;
use DomainException;

class FindTaskByIdService implements FindTaskByIdServiceContract
{
    public function __construct(
        private readonly UuidValidatorContract $uuidValidator,
        private readonly TaskRepositoryContract $taskRepository,
    ) {}

    private function checkTaskExists(Task|bool $task): void
    {
        throw_if($task === false, new DomainException(SystemMessage::RESOURCE_NOT_FOUND));
    }

    public function handle(string $id): Task
    {
        $this->uuidValidator->checkUuidIsValid($id);

        $task = $this->taskRepository->find($id);

        $this->checkTaskExists($task);

        return $task;
    }
}
