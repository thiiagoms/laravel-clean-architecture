<?php

namespace App\Http\Controllers\Api\Task;

use App\DTO\Task\Register\RegisterTaskDTO;
use App\DTO\Task\Update\UpdateTaskDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\Register\RegisterTaskRequest;
use App\Http\Requests\Task\Update\UpdateTaskRequest;
use App\Infrastructure\Persistence\Model\Task;
use App\Services\Task\Destroy\DestroyTaskService;
use App\Services\Task\Find\FindTasksByUserService;
use App\Services\Task\Register\RegisterTaskService;
use App\Services\Task\Update\UpdateTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use TaskResource;

class TaskApiController extends Controller
{
    public function __construct(
        private readonly FindTasksByUserService $findTasksByUserService,
        private readonly RegisterTaskService $registerTaskService,
        private readonly UpdateTaskService $updateTaskService,
        private readonly DestroyTaskService $destroyTaskService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->user('api')->id;

        $tasks = $this->findTasksByUserService->handle($userId);

        return TaskResource::collection($tasks);
    }

    #[OA\Post(
        path: '/api/task',
        tags: ['Task'],
        summary: 'Create new task',
        security: ['bearerAuth'],
        description: 'Create a new task and receive the task data upon successful creation.',
        requestBody: new OA\RequestBody(
            description: 'Task data for creation',
            required: true,
            content: new JsonContent(
                ref: '#/components/schemas/RegisterTaskVirtualRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Success response',
                content: new JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: '#/components/schemas/TaskVirtualResponse'
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'The server could not process the request due to invalid input.'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function store(RegisterTaskRequest $request): TaskResource
    {
        $registerTaskDTO = RegisterTaskDTO::fromRequest($request);

        $task = $this->registerTaskService->handle($registerTaskDTO);

        return TaskResource::make($task);
    }

    #[OA\Get(
        path: '/api/task/{id}',
        tags: ['Task'],
        summary: 'Retrieves the detailed task record for the authenticated user.',
        security: ['bearerAuth'],
        description: 'Retrieves the detailed task record for the authenticated user but only task that the authenticated user has permission to view will be returned or if user is admin all tasks will be returned.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The id (uuid) of the task record to be retrieved.',
                required: true,
                schema: new OA\Schema(
                    type: 'string'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success response',
                content: new JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: '#/components/schemas/TaskVirtualResponse'
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'The server could not process the request due to invalid input.'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        return TaskResource::make($task);
    }

    #[OA\Put(
        path: '/api/task/{id}',
        tags: ['Task'],
        summary: 'Update the specified task in database.',
        security: ['bearerAuth'],
        description: 'Update the specified task in database but only tasks that the authenticated user has permission to update will be updated except if user is admin.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The id (uuid) of the task record to be updated.',
                required: true,
                schema: new OA\Schema(
                    type: 'string'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'Task data for update',
            required: true,
            content: new JsonContent(
                ref: '#/components/schemas/RegisterTaskVirtualRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success response',
                content: new JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: '#/components/schemas/TaskVirtualResponse'
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'The server could not process the request due to invalid input.'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
        ]
    )]
    #[OA\Patch(
        path: '/api/task/{id}',
        tags: ['Task'],
        summary: 'Update the specified task in database.',
        security: ['bearerAuth'],
        description: 'Update the specified task in database but only tasks that the authenticated user has permission to update will be updated except if user is admin.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The id (uuid) of the task record to be updated.',
                required: true,
                schema: new OA\Schema(
                    type: 'string'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'Expense data for update',
            required: true,
            content: new JsonContent(
                ref: '#/components/schemas/RegisterTaskVirtualRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success response',
                content: new JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: '#/components/schemas/TaskVirtualResponse'
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'The server could not process the request due to invalid input.'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $updateTaskDTO = UpdateTaskDTO::fromRequest($request, $task);

        $task = $this->updateTaskService->handle($updateTaskDTO);

        return TaskResource::make($task);
    }

    #[OA\Delete(
        path: '/api/task/{id}',
        tags: ['Task'],
        summary: 'Remove the specified task from database.',
        security: ['bearerAuth'],
        description: 'Remove the specified task from database but only task that the authenticated user has permission to delete will be deleted or if user is admin.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The id (uuid) of the task record to be deleted.',
                required: true,
                schema: new OA\Schema(
                    type: 'string'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Operation success'
            ),
            new OA\Response(
                response: 400,
                description: 'The server could not process the request due to invalid input.'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $this->destroyTaskService->handle($task->id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
