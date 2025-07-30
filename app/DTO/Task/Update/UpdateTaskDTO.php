<?php

declare(strict_types=1);

namespace App\DTO\Task\Update;

use App\Contracts\DTO\Task\Update\UpdateTaskDTOContract;
use App\DTO\BaseDTO;
use App\Enums\Task\TaskStatusEnum;
use App\Http\Requests\Task\Update\UpdateTaskRequest;
use App\Models\Task;
use App\Support\Sanitizer;

class UpdateTaskDTO extends BaseDTO implements UpdateTaskDTOContract
{
    public function __construct(
        public readonly string $id,
        public readonly string $user_id,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?TaskStatusEnum $status = null
    ) {}

    public static function fromRequest(UpdateTaskRequest $request, Task $task): UpdateTaskDTO
    {
        $payload = Sanitizer::clean($request->validated());

        $payload['id'] = $task->id;

        if (isset($request->user_id)) {
            $payload['user_id'] = $request->user_id;
        } else {
            $payload['user_id'] = $task->user_id;
        }

        if (isset($request->status)) {
            $payload['status'] = TaskStatusEnum::from($request->status);
        }

        return new self(...$payload);
    }

    public static function fromArray(array $payload): UpdateTaskDTO
    {
        $payload = Sanitizer::clean($payload);

        if (isset($payload['status'])) {
            $payload['status'] = TaskStatusEnum::from($payload['status']);
        }

        return new self(...$payload);
    }
}
