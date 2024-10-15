<?php

namespace Tests\Unit\Services\Task\Find;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Exceptions\LogicalException;
use App\Messages\System\SystemMessage;
use App\Models\Task;
use App\Services\Task\Find\FindTaskByIdService;
use DomainException;
use Mockery;
use Tests\TestCase;

class FindTaskByIdServiceTest extends TestCase
{
    public function testItShouldThrowLogicalExceptionWithInvalidParameterMessageWhenTaskIdIsNotAValidUuid(): void
    {
        /** @var FindTaskByIdService $findTaskByIdService */
        $findTaskByIdService = resolve(FindTaskByIdService::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(SystemMessage::INVALID_PARAMETER);

        $findTaskByIdService->handle('invalid-uuid');
    }

    public function testItShouldThrowDomainExceptionWithResourceNotFoundMessageWhenTaskIdIsAValidUuidButDoesNotExist(): void
    {
        $taskId = fake()->uuid();

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('find')
            ->once()
            ->with($taskId)
            ->andReturnFalse();

        /** @var FindTaskByIdService $findTaskByIdService */
        $findTaskByIdService = resolve(FindTaskByIdService::class, ['taskRepository' => $taskRepositoryMock]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(SystemMessage::RESOURCE_NOT_FOUND);

        $findTaskByIdService->handle($taskId);

        Mockery::close();
    }

    public function testItShouldReturnTaskWhenTaskIdIsAValidUuidAndTaskExistsInDatabase(): void
    {
        $taskId = fake()->uuid();

        $taskMock = new Task(Task::factory()->raw(['id' => $taskId]));

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('find')
            ->once()
            ->with($taskId)
            ->andReturn($taskMock);

        /** @var FindTaskByIdService $findTaskByIdService */
        $findTaskByIdService = resolve(FindTaskByIdService::class, ['taskRepository' => $taskRepositoryMock]);

        /** @var Task $task */
        $task = $findTaskByIdService->handle($taskId);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($taskMock->toArray(), $task->toArray());

        Mockery::close();
    }
}
