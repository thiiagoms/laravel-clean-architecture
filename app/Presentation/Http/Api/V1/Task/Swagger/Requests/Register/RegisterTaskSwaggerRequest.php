<?php

namespace App\Presentation\Http\Api\V1\Task\Swagger\Requests\Register;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Task register request',
    description: 'Base request for create new task for authenticated user',
    type: 'object',
)]
class RegisterTaskSwaggerRequest
{
    #[OA\Property(

        property: 'title',
        description: 'The title of the task.',
        type: 'string',
        maxLength: 100,
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
    public string $status;
}
