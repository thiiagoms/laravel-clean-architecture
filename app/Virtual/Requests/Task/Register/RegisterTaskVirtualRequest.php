<?php

declare(strict_types=1);

namespace App\Virtual\Requests\Task\Register;

use App\Enums\Task\TaskTitleEnum;
use OpenApi\Attributes as OA;

#[OA\Schema(
    description: 'Base request for create new task for authenticated user',
    type: 'object',
    title: 'Task register request',
)]
class RegisterTaskVirtualRequest
{
    #[OA\Property(
        property: 'title',
        type: 'string',
        description: 'The title of the task.',
        maxLength: TaskTitleEnum::MAX_LENGTH->value,
        example: 'My first task',
    )]
    public string $title;

    #[OA\Property(
        property: 'description',
        type: 'string',
        description: 'The description of the task.',
        example: "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book"
    )]
    public string $description;

    #[OA\Property(
        property: 'status',
        type: 'string',
        description: 'The status of the task.',
        example: 'todo',
    )]
    public float|int $status;
}
