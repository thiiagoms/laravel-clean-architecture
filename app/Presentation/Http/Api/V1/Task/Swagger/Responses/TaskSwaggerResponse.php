<?php

namespace App\Presentation\Http\Api\V1\Task\Swagger\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Task Response',
    description: 'Task response',
    type: 'object',
)]
class TaskSwaggerResponse
{
    #[OA\Property(
        title: 'Id',
        description: 'The unique identifier of the task.',
        type: 'string',
        format: 'uuid',
    )]
    public string $id;

    #[OA\Property(
        property: 'title',
        description: 'The title of the task.',
        type: 'string',
        example: 'My first task',
    )]
    public string $title;

    #[OA\Property(
        property: 'description',
        description: 'The description of the task.',
        type: 'string',
        example: "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book"
    )]
    public string $description;

    #[OA\Property(
        property: 'status',
        description: 'The status of the task.',
        type: 'string',
        enum: ['todo', 'doing', 'done', 'cancelled'],
        example: 'todo',
    )]
    public float|int $status;

    #[OA\Property(
        title: 'created at',
        description: 'The date and time when the task was created',
        type: 'string',
        format: 'date-time',
        example: '2024-10-15 23:19:39',
    )]
    public string $created_at;

    #[OA\Property(
        title: 'updated at',
        description: 'The date and time when the task was updated',
        type: 'string',
        format: 'date-time',
        example: '2024-10-15 23:19:39',
    )]
    public string $updated_at;
}
