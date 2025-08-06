<?php

namespace Feature\Infrastructure\Persistence\Repository\Task\Update;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Status\Factory\StatusFactory;
use App\Domain\Entity\Task\Status\implementation\Todo;
use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\Task;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Infrastructure\Persistence\Mapper\User\UserMapper;
use App\Infrastructure\Persistence\Model\Task as LaravelTaskModel;
use App\Infrastructure\Persistence\Repository\Task\Update\EloquentUpdateTaskRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EloquentUpdateTaskRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private EloquentUpdateTaskRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentUpdateTaskRepository;
    }

    #[Test]
    public function it_should_throw_exception_when_try_to_update_task_that_does_not_exist_in_database(): void
    {
        $task = new Task(
            title: new Title('Initial Task Title'),
            description: new Description('Initial Task Description'),
            owner: new User(
                name: new Name('John Doen'),
                email: new Email('ilovelaravel@gmail.com'),
                password: new Password(password: 'P4sSw0rdSAD!@#)'),
                role: Role::USER,
                id: new Id('123e4567-e89b-12d3-a456-426614174000'),
            ),
            status: new Todo,
            id: new Id('123e4567-e89b-12d3-a456-426614174001'),
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to update task.');

        $this->repository->update($task);
    }

    #[Test]
    public function it_should_update_only_task_title(): void
    {
        $taskModel = LaravelTaskModel::factory()->createOne(['title' => 'Task title']);

        $task = new Task(
            title: new Title('Updated Task Title'),
            description: new Description($taskModel->description),
            owner: UserMapper::toDomain($taskModel->user),
            status: StatusFactory::map(Status::from($taskModel->status)),
            id: new Id($taskModel->id),
            createdAt: $taskModel->created_at->toDateTimeImmutable(),
            updatedAt: $taskModel->updated_at->toDateTimeImmutable(),
        );

        $result = $this->repository->update($task);

        $this->assertSame($task, $result);
    }

    #[Test]
    public function it_should_update_only_task_description(): void
    {
        $taskModel = LaravelTaskModel::factory()->createOne(['description' => 'Task Description']);

        $task = new Task(
            title: new Title($taskModel->title),
            description: new Description('Updated Task Description'),
            owner: UserMapper::toDomain($taskModel->user),
            status: StatusFactory::map(Status::from($taskModel->status)),
            id: new Id($taskModel->id),
            createdAt: $taskModel->created_at->toDateTimeImmutable(),
            updatedAt: $taskModel->updated_at->toDateTimeImmutable(),
        );

        $result = $this->repository->update($task);

        $this->assertSame($task, $result);
    }

    #[Test]
    public function it_should_update_only_task_status(): void
    {
        $taskModel = LaravelTaskModel::factory()->createOne(['status' => 'doing']);

        $task = new Task(
            title: new Title($taskModel->title),
            description: new Description($taskModel->description),
            owner: UserMapper::toDomain($taskModel->user),
            status: StatusFactory::map(Status::from('cancelled')),
            id: new Id($taskModel->id),
            createdAt: $taskModel->created_at->toDateTimeImmutable(),
            updatedAt: $taskModel->updated_at->toDateTimeImmutable(),
        );

        $result = $this->repository->update($task);

        $this->assertSame($task, $result);
    }

    #[Test]
    public function it_should_update_entire_task_data(): void
    {
        $taskModel = LaravelTaskModel::factory()->createOne([
            'title' => 'Task title',
            'description' => 'Task Description',
            'status' => 'todo',
        ]);

        $task = new Task(
            title: new Title('Updated Task Title'),
            description: new Description('Updated Task description'),
            owner: UserMapper::toDomain($taskModel->user),
            status: StatusFactory::map(Status::from('doing')),
            id: new Id($taskModel->id),
            createdAt: $taskModel->created_at->toDateTimeImmutable(),
            updatedAt: $taskModel->updated_at->toDateTimeImmutable(),
        );

        $result = $this->repository->update($task);

        $this->assertSame($task, $result);
    }
}
