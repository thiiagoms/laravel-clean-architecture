<?php

namespace App\Presentation\Http\Api\V1\Task\Controllers;

use App\Application\UseCases\Task\Destroy\DestroyTask;
use App\Application\UseCases\Task\Register\DTO\RegisterTaskDTO;
use App\Application\UseCases\Task\Register\RegisterTask;
use App\Application\UseCases\Task\Update\DTO\UpdateTaskDTO;
use App\Application\UseCases\Task\Update\UpdateTask;
use App\Domain\Common\ValueObject\Id;
use App\Infrastructure\Persistence\Mapper\Task\TaskMapper;
use App\Infrastructure\Persistence\Mapper\User\UserMapper;
use App\Infrastructure\Persistence\Model\Task as LaravelTaskModel;
use App\Presentation\Http\Api\Controller;
use App\Presentation\Http\Api\V1\Task\Requests\Register\RegisterTaskApiRequest;
use App\Presentation\Http\Api\V1\Task\Requests\Update\UpdateTaskApiRequest;
use App\Presentation\Http\Api\V1\Task\Resources\TaskResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response;

class TaskApiController extends Controller
{
    public function __construct(
        private readonly RegisterTask $registerTask,
        private readonly UpdateTask $updateTask,
        private readonly DestroyTask $destroyTask
    ) {}

    public function index(): void {}

    #[OA\Post(
        path: '/api/v1/task',
        description: 'Create a new task and receive the task data upon successful creation.',
        summary: 'Create new task',
        security: ['bearerAuth'],
        requestBody: new OA\RequestBody(
            description: 'Task data for creation',
            required: true,
            content: new JsonContent(
                ref: '#/components/schemas/RegisterTaskSwaggerRequest'
            )
        ),
        tags: ['Task'],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Success response',
                content: new JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'id',
                            description: 'The unique identifier of the task.',
                            type: 'string',
                            format: 'uuid'
                        ),
                        new OA\Property(
                            property: 'title',
                            description: 'The title of the task.',
                            type: 'string',
                            example: 'My first task'
                        ),
                        new OA\Property(
                            property: 'description',
                            description: 'The description of the task.',
                            type: 'string',
                            example: 'Lorem Ipsum is simply dummy text...'
                        ),
                        new OA\Property(
                            property: 'status',
                            description: 'The status of the task.',
                            type: 'string',
                            enum: ['todo', 'doing', 'done', 'cancelled'],
                            example: 'todo'
                        ),
                        new OA\Property(
                            property: 'created_at',
                            description: 'The date and time when the task was created.',
                            type: 'string',
                            format: 'date-time',
                            example: '2024-10-15 23:19:39'
                        ),
                        new OA\Property(
                            property: 'updated_at',
                            description: 'The date and time when the task was updated.',
                            type: 'string',
                            format: 'date-time',
                            example: '2024-10-15 23:19:39'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'The server could not process the request due to invalid input.'
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function store(RegisterTaskApiRequest $request): JsonResponse
    {
        $dto = RegisterTaskDTO::fromRequest($request);

        $task = $this->registerTask->handle($dto);

        return response()->json(data: ['data' => TaskResource::make($task)], status: Response::HTTP_CREATED);
    }

    /**
     * @throws AuthorizationException
     */
    #[OA\Get(
        path: '/api/v1/task/{id}',
        description: 'Retrieves the detailed task record for the authenticated user but only task that the authenticated user has permission to view will be returned or if user is admin all tasks will be returned.',
        summary: 'Retrieves the detailed task record for the authenticated user.',
        security: ['bearerAuth'],
        tags: ['Task'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The id (uuid) of the task record to be retrieved.',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'string'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Success response',
                content: new JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'id',
                            description: 'The unique identifier of the task.',
                            type: 'string',
                            format: 'uuid'
                        ),
                        new OA\Property(
                            property: 'title',
                            description: 'The title of the task.',
                            type: 'string',
                            example: 'My first task'
                        ),
                        new OA\Property(
                            property: 'description',
                            description: 'The description of the task.',
                            type: 'string',
                            example: 'Lorem Ipsum is simply dummy text...'
                        ),
                        new OA\Property(
                            property: 'status',
                            description: 'The status of the task.',
                            type: 'string',
                            enum: ['todo', 'doing', 'done', 'cancelled'],
                            example: 'todo'
                        ),
                        new OA\Property(
                            property: 'created_at',
                            description: 'The date and time when the task was created.',
                            type: 'string',
                            format: 'date-time',
                            example: '2024-10-15 23:19:39'
                        ),
                        new OA\Property(
                            property: 'updated_at',
                            description: 'The date and time when the task was updated.',
                            type: 'string',
                            format: 'date-time',
                            example: '2024-10-15 23:19:39'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'The server could not process the request due to invalid input.'
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function show(LaravelTaskModel $task): TaskResource
    {
        $this->authorize('view', $task);

        $task = TaskMapper::toDomain(model: $task, owner: UserMapper::toDomain($task->user));

        return TaskResource::make($task);
    }

    #[OA\Put(
        path: '/api/v1/task/{id}',
        description: 'Update the specified task in database but only tasks that the authenticated user has permission to update will be updated except if user is admin.',
        summary: 'Update the specified task in database.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            description: 'Task data for update',
            required: true,
            content: new JsonContent(
                required: ['title', 'description', 'status'],
                properties: [
                    new OA\Property(
                        property: 'title',
                        description: 'The title of the task.',
                        type: 'string',
                        maxLength: 100,
                        example: 'My first task'
                    ),
                    new OA\Property(
                        property: 'description',
                        description: 'The description of the task.',
                        type: 'string',
                        example: 'Lorem Ipsum is simply dummy text...'
                    ),
                    new OA\Property(
                        property: 'status',
                        description: 'The status of the task.',
                        type: 'string',
                        enum: ['todo', 'doing', 'done', 'cancelled'],
                        example: 'todo'
                    ),
                ],
                type: 'object'
            )
        ),
        tags: ['Task'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The id (uuid) of the task record to be updated.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Success response',
                content: new JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Task updated successfully.'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'The server could not process the request due to invalid input.'),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthorized'
            ),
        ]
    )]
    #[OA\Patch(
        path: '/api/v1/task/{id}',
        description: 'Update the specified task in database but only tasks that the authenticated user has permission to update will be updated except if user is admin.',
        summary: 'Update the specified task in database.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            description: 'Task data for partial update',
            required: true,
            content: new JsonContent(
                properties: [
                    new OA\Property(
                        property: 'title',
                        description: 'The title of the task.',
                        type: 'string',
                        maxLength: 100,
                        example: 'My updated title'
                    ),
                    new OA\Property(
                        property: 'description',
                        description: 'The description of the task.',
                        type: 'string',
                        example: 'Updated description...'
                    ),
                    new OA\Property(
                        property: 'status',
                        description: 'The status of the task.',
                        type: 'string',
                        enum: ['todo', 'doing', 'done', 'cancelled'],
                        example: 'done'
                    ),
                ],
                type: 'object'
            )
        ),
        tags: ['Task'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The id (uuid) of the task record to be updated.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Success response',
                content: new JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Task updated successfully.'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'The server could not process the request due to invalid input.'
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function update(LaravelTaskModel $task, UpdateTaskApiRequest $request): TaskResource
    {
        $this->authorize('update', $task);

        $dto = UpdateTaskDTO::fromRequest(request: $request, id: new Id($task->id));

        $task = $this->updateTask->handle($dto);

        return TaskResource::make($task);
    }

    #[OA\Delete(
        path: '/api/v1/task/{id}',
        description: 'Remove the specified task from database but only task that the authenticated user has permission to delete will be deleted or if user is admin.',
        summary: 'Remove the specified task from database.',
        security: ['bearerAuth'],
        tags: ['Task'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The id (uuid) of the task record to be deleted.',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'string'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_NO_CONTENT,
                description: 'Operation success'
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Resource not found'
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function destroy(LaravelTaskModel $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->destroyTask->handle(new Id($task->id));

        return response()->json(data: [], status: Response::HTTP_NO_CONTENT);
    }
}
