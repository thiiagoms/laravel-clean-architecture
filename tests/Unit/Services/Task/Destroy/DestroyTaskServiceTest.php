<?php

namespace Tests\Unit\Services\Task\Destroy;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Services\Task\Find\FindTaskByIdServiceContract;
use App\Exceptions\LogicalException;
use App\Messages\System\SystemMessage;
use App\Models\Task;
use App\Services\Task\Destroy\DestroyTaskService;
use DomainException;
use Mockery;
use Tests\TestCase;

class DestroyTaskServiceTest extends TestCase
{
    public function testItShouldThrowLogicalExceptionWithInvalidParameterMessageWhenTaskIdIsNotAValidUuid(): void
    {
        /** @var DestroyTaskService $destroyTaskService */
        $destroyTaskService = resolve(DestroyTaskService::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(SystemMessage::INVALID_PARAMETER);

        $destroyTaskService->handle('invalid-id');
    }

    public function testItShouldThrowDomainExceptionWithResourceNotFoundMessageWhenTaskIdIsAValidUuidButDoesNotExist(): void
    {
        $taskId = fake()->uuid();

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($taskId)
            ->andThrow(new DomainException(SystemMessage::RESOURCE_NOT_FOUND));

        /** @var DestroyTaskService $destroyTaskService */
        $destroyTaskService = resolve(DestroyTaskService::class, ['findTaskByIdService' => $findTaskByIdServiceMock]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(SystemMessage::RESOURCE_NOT_FOUND);

        $destroyTaskService->handle($taskId);

        Mockery::close();
    }

    public function testItShouldDestroyTaskWhenTaskIdIsAValidUuidAndExistsInDatabase(): void
    {
        $taskId = fake()->uuid();

        $task = new Task(Task::factory()->raw(['id' => $taskId]));

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($taskId)
            ->andReturn($task);

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('destroy')
            ->once()
            ->with($taskId)
            ->andReturnTrue();

        /** @var DestroyTaskService $destroyTaskService */
        $destroyTaskService = resolve(DestroyTaskService::class, [
            'findTaskByIdService' => $findTaskByIdServiceMock,
            'taskRepository' => $taskRepositoryMock,
        ]);

        $result = $destroyTaskService->handle($taskId);

        $this->assertIsBool($result);
        $this->assertTrue($result);

        Mockery::close();
    }
}
