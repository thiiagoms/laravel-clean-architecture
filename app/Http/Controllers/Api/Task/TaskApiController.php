<?php

namespace App\Http\Controllers\Api\Task;

use App\DTO\Task\Register\RegisterTaskDTO;
use App\DTO\Task\Update\UpdateTaskDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\Register\RegisterTaskRequest;
use App\Http\Requests\Task\Update\UpdateTaskRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Services\Task\Register\RegisterTaskService;
use App\Services\Task\Update\UpdateTaskService;

class TaskApiController extends Controller
{
    public function __construct(
        private readonly RegisterTaskService $registerTaskService,
        private readonly UpdateTaskService $updateTaskService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterTaskRequest $request): TaskResource
    {
        $registerTaskDTO = RegisterTaskDTO::fromRequest($request);

        $task = $this->registerTaskService->handle($registerTaskDTO);

        return TaskResource::make($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        return TaskResource::make($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $updateTaskDTO = UpdateTaskDTO::fromRequest($request, $task);

        $task = $this->updateTaskService->handle($updateTaskDTO);

        return TaskResource::make($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
