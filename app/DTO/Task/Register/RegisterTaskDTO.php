<?php

declare(strict_types=1);

namespace App\DTO\Task\Register;

use App\Contracts\DTO\Task\Register\RegisterTaskDTOContract;
use App\DTO\BaseDTO;
use App\Enums\Task\TaskStatusEnum;
use App\Http\Requests\Task\Register\RegisterTaskRequest;

class RegisterTaskDTO extends BaseDTO implements RegisterTaskDTOContract
{
    public function __construct(
        public readonly string $user_id,
        public readonly string $title,
        public readonly string $description,
        public TaskStatusEnum $status
    ) {}

    public static function fromRequest(RegisterTaskRequest $request): RegisterTaskDTO
    {
        $payload = clean($request->validated());

        $payload['status'] = TaskStatusEnum::from($payload['status']);

        $payload['user_id'] = $request->user('api')->id;

        return new self(...$payload);
    }

    public static function fromArray(array $payload): RegisterTaskDTO
    {
        $payload = clean($payload);

        $payload['status'] = TaskStatusEnum::from($payload['status']);

        return new self(...$payload);
    }
}
