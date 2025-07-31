<?php

namespace Tests\Unit\Services\Task\Find;

use App\Contracts\Repositories\Task\TaskRepositoryContract;
use App\Contracts\Services\User\Find\FindUserByIdServiceContract;
use App\Exceptions\LogicalException;
use App\Infrastructure\Persistence\Model\User;
use App\Messages\System\SystemMessage;
use App\Models\Task;
use App\Services\Task\Find\FindTasksByUserService;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class FindTasksByUserServiceTest extends TestCase
{
    public function testItShouldThrowLogicalExceptionWithInvalidParameterMessageWhenUserIsIsNotAValidUuid(): void
    {
        /** @var FindTasksByUserService $findTasksByUserService */
        $findTasksByUserService = resolve(FindTasksByUserService::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(SystemMessage::INVALID_PARAMETER);

        $findTasksByUserService->handle('invalid-id');
    }

    public function testItShouldThrowDomainExceptionWithNotFoundMessageWhenUserIdIsAValidUuidButDoesNotExist(): void
    {
        $userId = fake()->uuid();

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($userId)
            ->andThrow(new DomainException(SystemMessage::RESOURCE_NOT_FOUND));

        /** @var FindTasksByUserService $findTasksByUserService */
        $findTasksByUserService = resolve(FindTasksByUserService::class, [
            'findUserByIdService' => $findUserByIdServiceMock,
        ]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(SystemMessage::RESOURCE_NOT_FOUND);

        $findTasksByUserService->handle($userId);

        Mockery::close();
    }

    public function testItShouldReturnAnEmptyCollectionWhenTheUserIdIsAValidUuidAndTheUserExistsButHasNoTasks(): void
    {
        $userId = fake()->uuid();

        $userMock = new User(User::factory()->raw(['id' => $userId]));

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($userId)
            ->andReturn($userMock);

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('findUserTasks')
            ->once()
            ->with($userId)
            ->andReturn(new Collection([]));

        /** @var FindTasksByUserService $findTasksByUserService */
        $findTasksByUserService = resolve(FindTasksByUserService::class, [
            'findUserByIdService' => $findUserByIdServiceMock,
            'taskRepository' => $taskRepositoryMock,
        ]);

        /** @var Collection $tasks */
        $tasks = $findTasksByUserService->handle($userId);

        $this->assertInstanceOf(Collection::class, $tasks);
        $this->assertCount(0, $tasks);

        Mockery::close();
    }

    public function testItShouldReturnTasksCollectionWhenTheUserIdIsAValidUuidAndTheUserExistsAndHasTasks(): void
    {
        $userId = fake()->uuid();

        $userMock = new User(['id' => $userId]);

        $tasksMock = [];

        for ($i = 0; $i < 10; $i++) {
            array_push($tasksMock, new Task(['user_id' => $userId]));
        }

        $findUserByIdServiceMock = Mockery::mock(FindUserByIdServiceContract::class);

        $findUserByIdServiceMock->shouldReceive('handle')
            ->once()
            ->with($userId)
            ->andReturn($userMock);

        $taskRepositoryMock = Mockery::mock(TaskRepositoryContract::class);

        $taskRepositoryMock->shouldReceive('findUserTasks')
            ->once()
            ->with($userId)
            ->andReturn(new Collection($tasksMock));

        /** @var FindTasksByUserService $findTasksByUserService */
        $findTasksByUserService = resolve(FindTasksByUserService::class, [
            'findUserByIdService' => $findUserByIdServiceMock,
            'taskRepository' => $taskRepositoryMock,
        ]);

        /** @var Collection $tasks */
        $tasks = $findTasksByUserService->handle($userId);

        $this->assertInstanceOf(Collection::class, $tasks);
        $this->assertCount(10, $tasks);

        Mockery::close();
    }
}
