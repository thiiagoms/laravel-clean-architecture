<?php

namespace Tests\Unit\Services\Task\Update;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Services\Task\Find\FindTaskByIdServiceContract;
use App\Contracts\Services\User\Find\FindUserByIdServiceContract;
use App\DTO\Task\Update\UpdateTaskDTO;
use App\Enums\Task\TaskStatusEnum;
use App\Exceptions\LogicalException;
use App\Messages\System\SystemMessage;
use App\Models\Task;
use App\Services\Task\Update\UpdateTaskService;
use DomainException;
use Mockery;
use Tests\TestCase;

class UpdateTaskServiceTest extends TestCase
{
    public function testItShouldThrowLogicalExceptionWithInvalidParameterMessageWhenTaskIdIsNotAValidUuid(): void
    {
        $updateTaskDTO = UpdateTaskDTO::fromArray(['id' => 'invalid-id', 'user_id' => 'invalid-id']);

        /** @var UpdateTaskService $updateTaskService */
        $updateTaskService = resolve(UpdateTaskService::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(SystemMessage::INVALID_PARAMETER);

        $updateTaskService->handle($updateTaskDTO);
    }

    public function testItShouldThrowDomainExceptionWithResourceNotFoundMessageWhenTaskIdIsAValidUuidButDoesNotExist(): void
    {
        $updateTaskDTO = UpdateTaskDTO::fromArray(['id' => fake()->uuid(), 'user_id' => 'invalid-id']);

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($updateTaskDTO->id)
            ->andThrow(new DomainException(SystemMessage::RESOURCE_NOT_FOUND));

        /** @var UpdateTaskService $updateTaskService */
        $updateTaskService = resolve(UpdateTaskService::class, ['findTaskByIdService' => $findTaskByIdServiceMock]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(SystemMessage::RESOURCE_NOT_FOUND);

        $updateTaskService->handle($updateTaskDTO);

        Mockery::close();
    }

    public function testItShouldThrowLogicalExceptionWithInvalidParameterMessageWhenUserIsIsNotAValidUuid(): void
    {
        $taskMock = new Task(Task::factory()->raw(['id' => fake()->uuid()]));

        $updateTaskDTO = UpdateTaskDTO::fromArray(['id' => $taskMock->id, 'user_id' => 'invalid-id']);

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($updateTaskDTO->id)
            ->andReturn($taskMock);

        /** @var UpdateTaskService $updateTaskService */
        $updateTaskService = resolve(UpdateTaskService::class, ['findTaskByIdService' => $findTaskByIdServiceMock]);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(SystemMessage::INVALID_PARAMETER);

        $updateTaskService->handle($updateTaskDTO);

        Mockery::close();
    }

    public function testItShouldThrowDomainExceptionWithResourceNotFoundMessageWhenUserIdIsAValidUuidButDoesNotExist(): void
    {
        $taskMock = new Task(Task::factory()->raw(['id' => fake()->uuid()]));

        $updateTaskDTO = UpdateTaskDTO::fromArray(['id' => $taskMock->id, 'user_id' => $taskMock->user->id]);

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($updateTaskDTO->id)
            ->andReturn($taskMock);

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($updateTaskDTO->user_id)
            ->andThrow(new DomainException(SystemMessage::RESOURCE_NOT_FOUND));

        /** @var UpdateTaskService $updateTaskService */
        $updateTaskService = resolve(UpdateTaskService::class, [
            'findTaskByIdService' => $findTaskByIdServiceMock,
            'findUserByIdService' => $findUserByIdServiceMock,
        ]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(SystemMessage::RESOURCE_NOT_FOUND);

        $updateTaskService->handle($updateTaskDTO);

        Mockery::close();
    }

    public function testItShouldUpdateOnlyUserIdWhenOnlyUserIdIIsProvidedAndReturnUpdatedTask(): void
    {
        $taskMock = new Task(Task::factory()->raw(['id' => fake()->uuid()]));

        $updatedTaskMock = new Task(Task::factory()->raw(['id' => $taskMock->id]));

        $updateTaskDTO = UpdateTaskDTO::fromArray(['id' => $taskMock->id, 'user_id' => $taskMock->user->id]);

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->twice()
            ->with($updateTaskDTO->id)
            ->andReturnUsing(fn (): Task => $taskMock, fn (): Task => $updatedTaskMock);

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($updateTaskDTO->user_id)
            ->andReturn($taskMock->user);

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('update')
            ->once()
            ->with($updateTaskDTO->id, removeEmpty($updateTaskDTO->toArray()))
            ->andReturnTrue();

        /** @var UpdateTaskService $updateTaskService */
        $updateTaskService = resolve(UpdateTaskService::class, [
            'findTaskByIdService' => $findTaskByIdServiceMock,
            'findUserByIdService' => $findUserByIdServiceMock,
            'taskRepository' => $taskRepositoryMock,
        ]);

        $task = $updateTaskService->handle($updateTaskDTO);

        $this->assertInstanceOf(Task::class, $task);

        $this->assertEquals($task->toArray(), $updatedTaskMock->toArray());
        $this->assertEquals($task->user->toArray(), $updatedTaskMock->user->toArray());
        $this->assertNotEquals($task->toArray(), $taskMock->toArray());

        Mockery::close();
    }

    public function testItShouldUpdateOnlyTaskTitleWhenOnlyTitleIIsProvidedAndReturnUpdatedTask(): void
    {
        $taskMock = new Task(Task::factory()->raw());

        $updateTaskDTO = UpdateTaskDTO::fromArray([
            'id' => $taskMock->id,
            'user_id' => $taskMock->user->id,
            'title' => 'updated title',
        ]);

        $updatedTaskMock = $taskMock->replicate();
        $updatedTaskMock->title = 'updated title';

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->twice()
            ->with($updateTaskDTO->id)
            ->andReturnUsing(fn (): Task => $taskMock, fn (): Task => $updatedTaskMock);

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($updateTaskDTO->user_id)
            ->andReturn($taskMock->user);

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('update')
            ->once()
            ->with($updateTaskDTO->id, removeEmpty($updateTaskDTO->toArray()))
            ->andReturnTrue();

        /** @var UpdateTaskService $updateTaskService */
        $updateTaskService = resolve(UpdateTaskService::class, [
            'findTaskByIdService' => $findTaskByIdServiceMock,
            'findUserByIdService' => $findUserByIdServiceMock,
            'taskRepository' => $taskRepositoryMock,
        ]);

        $task = $updateTaskService->handle($updateTaskDTO);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($task->toArray(), $updatedTaskMock->toArray());
        $this->assertEquals($task->title, $updatedTaskMock->title);
        $this->assertNotEquals($task->toArray(), $taskMock->toArray());
        $this->assertNotEquals($task->title, $taskMock->title);

        Mockery::close();
    }

    public function testItShouldUpdateOnlyTaskDescriptionWhenOnlyDescriptionIIsProvidedAndReturnUpdatedTask(): void
    {
        $taskMock = new Task(Task::factory()->raw());

        $updateTaskDTO = UpdateTaskDTO::fromArray([
            'id' => $taskMock->id,
            'user_id' => $taskMock->user->id,
            'description' => 'updated description',
        ]);

        $updatedTaskMock = $taskMock->replicate();
        $updatedTaskMock->description = 'updated description';

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->twice()
            ->with($updateTaskDTO->id)
            ->andReturnUsing(fn (): Task => $taskMock, fn (): Task => $updatedTaskMock);

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($updateTaskDTO->user_id)
            ->andReturn($taskMock->user);

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('update')
            ->once()
            ->with($updateTaskDTO->id, removeEmpty($updateTaskDTO->toArray()))
            ->andReturnTrue();

        /** @var UpdateTaskService $updateTaskService */
        $updateTaskService = resolve(UpdateTaskService::class, [
            'findTaskByIdService' => $findTaskByIdServiceMock,
            'findUserByIdService' => $findUserByIdServiceMock,
            'taskRepository' => $taskRepositoryMock,
        ]);

        $task = $updateTaskService->handle($updateTaskDTO);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($task->toArray(), $updatedTaskMock->toArray());
        $this->assertEquals($task->description, $updatedTaskMock->description);
        $this->assertNotEquals($task->toArray(), $taskMock->toArray());
        $this->assertNotEquals($task->description, $taskMock->description);

        Mockery::close();
    }

    public function testItShouldUpdateOnlyTaskStatusWhenOnlyStatusIIsProvidedAndReturnUpdatedTask(): void
    {
        $taskMock = new Task(Task::factory()->raw());

        $updateTaskDTO = UpdateTaskDTO::fromArray([
            'id' => $taskMock->id,
            'user_id' => $taskMock->user->id,
            'status' => 'done',
        ]);

        $updatedTaskMock = $taskMock->replicate();
        $updatedTaskMock->status = TaskStatusEnum::DONE;

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->twice()
            ->with($updateTaskDTO->id)
            ->andReturnUsing(fn (): Task => $taskMock, fn (): Task => $updatedTaskMock);

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($updateTaskDTO->user_id)
            ->andReturn($taskMock->user);

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('update')
            ->once()
            ->with($updateTaskDTO->id, removeEmpty($updateTaskDTO->toArray()))
            ->andReturnTrue();

        /** @var UpdateTaskService $updateTaskService */
        $updateTaskService = resolve(UpdateTaskService::class, [
            'findTaskByIdService' => $findTaskByIdServiceMock,
            'findUserByIdService' => $findUserByIdServiceMock,
            'taskRepository' => $taskRepositoryMock,
        ]);

        $task = $updateTaskService->handle($updateTaskDTO);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($task->toArray(), $updatedTaskMock->toArray());
        $this->assertEquals($task->status, $updatedTaskMock->status);
        $this->assertNotEquals($task->toArray(), $taskMock->toArray());
        $this->assertNotEquals($task->status, $taskMock->status);

        Mockery::close();
    }

    public function testItShouldUpdateAllTaskFieldsWhenAllFieldsIIsProvidedAndReturnUpdatedTask(): void
    {
        $taskMock = new Task(Task::factory()->raw());

        $updatedTaskMock = new Task(Task::factory()->raw([
            'id' => $taskMock->id,
            'title' => 'updated title',
            'description' => 'updated description',
            'status' => TaskStatusEnum::DONE,
        ]));

        $updateTaskDTO = UpdateTaskDTO::fromArray([
            'id' => $taskMock->id,
            'user_id' => $updatedTaskMock->user->id,
            'title' => 'updated title',
            'description' => 'updated description',
            'status' => 'done',
        ]);

        $findTaskByIdServiceMock = Mockery::mock(FindTaskByIdServiceContract::class);

        $findTaskByIdServiceMock->shouldReceive('handle')
            ->twice()
            ->with($updateTaskDTO->id)
            ->andReturnUsing(fn (): Task => $taskMock, fn (): Task => $updatedTaskMock);

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($updateTaskDTO->user_id)
            ->andReturn($updatedTaskMock->user);

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('update')
            ->once()
            ->with($updateTaskDTO->id, removeEmpty($updateTaskDTO->toArray()))
            ->andReturnTrue();

        /** @var UpdateTaskService $updateTaskService */
        $updateTaskService = resolve(UpdateTaskService::class, [
            'findTaskByIdService' => $findTaskByIdServiceMock,
            'findUserByIdService' => $findUserByIdServiceMock,
            'taskRepository' => $taskRepositoryMock,
        ]);

        $task = $updateTaskService->handle($updateTaskDTO);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($task->toArray(), $updatedTaskMock->toArray());
        $this->assertEquals($task->user->id, $updatedTaskMock->user->id);
        $this->assertEquals($task->title, $updatedTaskMock->title);
        $this->assertEquals($task->description, $updatedTaskMock->description);
        $this->assertEquals($task->status, $updatedTaskMock->status);
        $this->assertNotEquals($task->toArray(), $taskMock->toArray());
        $this->assertNotEquals($task->user->id, $taskMock->user->id);
        $this->assertNotEquals($task->title, $taskMock->title);
        $this->assertNotEquals($task->description, $taskMock->description);
        $this->assertNotEquals($task->status, $taskMock->status);

        Mockery::close();
    }
}
