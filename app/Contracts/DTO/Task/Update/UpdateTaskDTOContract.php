<?php

declare(strict_types=1);

namespace App\Contracts\DTO\Task\Update;

use App\Contracts\DTO\BaseDTOContract;
use App\DTO\Task\Update\UpdateTaskDTO;
use App\Http\Requests\Task\Update\UpdateTaskRequest;
use App\Infrastructure\Persistence\Model\Task;

interface UpdateTaskDTOContract extends BaseDTOContract
{
    public static function fromRequest(UpdateTaskRequest $request, Task $task): UpdateTaskDTO;

    public static function fromArray(array $payload): UpdateTaskDTO;
}
