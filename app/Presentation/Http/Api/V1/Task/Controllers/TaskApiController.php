<?php

namespace App\Presentation\Http\Api\V1\Task\Controllers;

use App\Application\UseCases\Task\Register\DTO\RegisterTaskDTO;
use App\Application\UseCases\Task\Register\RegisterTask;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Api\V1\Task\Requests\Register\RegisterTaskApiRequest;
use App\Presentation\Http\Api\V1\Task\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskApiController extends Controller
{
    public function __construct(private readonly RegisterTask $registerTask) {}

    public function store(RegisterTaskApiRequest $request): JsonResponse
    {
        $dto = RegisterTaskDTO::fromRequest($request);

        $task = $this->registerTask->handle($dto);

        return response()->json(data: ['data' => TaskResource::make($task)], status: Response::HTTP_CREATED);
    }
}
