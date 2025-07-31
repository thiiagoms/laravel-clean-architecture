<?php

namespace Tests\Unit\Services\Task\Register;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Services\User\Find\FindUserByIdServiceContract;
use App\DTO\Task\Register\RegisterTaskDTO;
use App\Exceptions\LogicalException;
use App\Infrastructure\Persistence\Model\User;
use App\Messages\System\SystemMessage;
use App\Models\Task;
use App\Services\Task\Register\RegisterTaskService;
use DomainException;
use Mockery;
use Tests\TestCase;

class RegisterTaskServiceTest extends TestCase
{
    public function testItShouldThrowLogicalExceptionWithInvalidParameterMessageWhenUserIsIsNotAValidUuid(): void
    {
        $registerTaskDTO = RegisterTaskDTO::fromArray([
            'user_id' => fake()->numerify('##########'),
            'title' => 'Task Title',
            'description' => fake()->name(),
            'status' => 'todo',
        ]);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(SystemMessage::INVALID_PARAMETER);

        /** @var RegisterTaskService $registerTaskService */
        $registerTaskService = resolve(RegisterTaskService::class);

        $registerTaskService->handle($registerTaskDTO);
    }

    public function testItShouldThrowDomainExceptionWithNotFoundMessageWhenUserIdIsAValidUuidButDoesNotExist(): void
    {
        $registerTaskDTO = RegisterTaskDTO::fromArray([
            'user_id' => fake()->uuid(),
            'title' => 'Task Title',
            'description' => fake()->name(),
            'status' => 'todo',
        ]);

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($registerTaskDTO->user_id)
            ->andThrow(new DomainException(SystemMessage::RESOURCE_NOT_FOUND));

        /** @var RegisterTaskService $registerTaskService */
        $registerTaskService = resolve(RegisterTaskService::class, [
            'findUserByIdService' => $findUserByIdServiceMock,
        ]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(SystemMessage::RESOURCE_NOT_FOUND);

        $registerTaskService->handle($registerTaskDTO);

        Mockery::close();
    }

    public function testITShouldCreateTaskWhenTaskDataAreValidAndReturnCreatedTask(): void
    {
        $registerTaskDTO = RegisterTaskDTO::fromArray([
            'user_id' => fake()->uuid(),
            'title' => 'Task Title',
            'description' => fake()->name(),
            'status' => 'todo',
        ]);

        $userMock = new User(User::factory()->raw(['id' => $registerTaskDTO->user_id]));

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($registerTaskDTO->user_id)
            ->andReturn($userMock);

        $taskMock = new Task(['id' => fake()->uuid(), ...$registerTaskDTO->toArray()]);

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('create')
            ->once()
            ->with($registerTaskDTO->toArray())
            ->andReturn($taskMock);

        /** @var RegisterTaskService $registerTaskService */
        $registerTaskService = resolve(RegisterTaskService::class, [
            'findUserByIdService' => $findUserByIdServiceMock,
            'taskRepository' => $taskRepositoryMock,
        ]);

        $task = $registerTaskService->handle($registerTaskDTO);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($taskMock->toArray(), $task->toArray());

        Mockery::close();
    }
}
