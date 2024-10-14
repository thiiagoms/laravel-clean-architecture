<?php

namespace App\Http\Controllers\Api\Task;

use App\DTO\Task\Register\RegisterTaskDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\Register\RegisterTaskRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Services\Task\Register\RegisterTaskService;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    public function __construct(private readonly RegisterTaskService $registerTaskService) {}

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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
