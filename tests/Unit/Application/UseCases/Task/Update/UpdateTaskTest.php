<?php

namespace Tests\Unit\Application\UseCases\Task\Update;

use App\Application\UseCases\Task\Common\Exception\TaskNotFoundException;
use App\Application\UseCases\Task\Common\Service\FindOrFailTaskByIdService;
use App\Application\UseCases\Task\Update\DTO\UpdateTaskDTO;
use App\Application\UseCases\Task\Update\Exception\TaskCanNotBeUpdatedException;
use App\Application\UseCases\Task\Update\Service\UpdateTaskService;
use App\Application\UseCases\Task\Update\UpdateTask;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Status\Factory\StatusFactory;
use App\Domain\Entity\Task\Status\implementation\Cancelled;
use App\Domain\Entity\Task\Status\implementation\Done;
use App\Domain\Entity\Task\Status\implementation\Todo;
use App\Domain\Entity\Task\Status\Interface\StatusInterface;
use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\Task;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpdateTaskTest extends TestCase
{
    private FindOrFailTaskByIdService|MockObject $findOrFailTaskByIdService;

    private UpdateTaskService|MockObject $updateTaskService;

    private UpdateTask $updateTask;

    private Task $taskExists;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->taskExists = new Task(
            title: new Title('Initial Task Title'),
            description: new Description('Initial Task Description'),
            owner: new User(
                name: new Name('John Doe'),
                email: new Email('ilovelaravel@gmail.com'),
                password: new Password(password: 'P4sSw0rdSAD!@#)'),
                role: Role::USER,
                id: new Id('123e4567-e89b-12d3-a456-426614174000'),
            ),
            status: new Todo,
            id: new Id('123e4567-e89b-12d3-a456-426614174001'),
        );

        $this->findOrFailTaskByIdService = $this->createMock(FindOrFailTaskByIdService::class);
        $this->updateTaskService = $this->createMock(UpdateTaskService::class);

        $this->updateTask = new UpdateTask(
            findOrFailTaskByIdService: $this->findOrFailTaskByIdService,
            service: $this->updateTaskService
        );
    }

    #[Test]
    public function it_should_throw_exception_when_task_does_not_exists(): void
    {
        $dto = new UpdateTaskDTO(id: new Id('e5e048b0-78dd-464e-b3f1-131b7ff8873d'));

        $this->findOrFailTaskByIdService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($dto->getId())
            ->willThrowException(TaskNotFoundException::create());

        $this->updateTaskService
            ->expects($this->never())
            ->method('update');

        $this->expectException(TaskNotFoundException::class);
        $this->expectExceptionMessage('Task not found');

        $this->updateTask->handle($dto);
    }

    public static function statusProvider(): array
    {
        return [
            'should throw exception when try to update task with done status' => [new Done],
            'should throw exception when try to update task with cancelled status' => [new Cancelled],
        ];
    }

    #[Test]
    #[DataProvider('statusProvider')]
    public function it_should_throw_exception_when_task_cannot_be_updated(StatusInterface $status): void
    {
        $dto = new UpdateTaskDTO(
            id: $this->taskExists->getId()
        );

        $this->findOrFailTaskByIdService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($dto->getId())
            ->willReturn(
                new Task(
                    title: new Title('Initial Task Title'),
                    description: new Description('Initial Task Description'),
                    owner: new User(
                        name: new Name('John Doe'),
                        email: new Email('ilovelaravel@gmail.com'),
                        password: new Password(password: 'P4sSw0rdSAD!@#)'),
                        role: Role::USER,
                        id: new Id('123e4567-e89b-12d3-a456-426614174000'),
                    ),
                    status: $status,
                    id: $this->taskExists->getId(),
                )
            );

        $this->updateTaskService
            ->expects($this->never())
            ->method('update');

        $this->expectException(TaskCanNotBeUpdatedException::class);
        $this->expectExceptionMessage('Task cannot be updated');

        $this->updateTask->handle($dto);
    }

    #[Test]
    public function it_should_update_only_task_title(): void
    {
        $dto = new UpdateTaskDTO(
            id: $this->taskExists->getId(),
            title: new Title('Updated Task Title')
        );

        $this->findOrFailTaskByIdService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($dto->getId())
            ->willReturn($this->taskExists);

        $this->updateTaskService
            ->expects($this->once())
            ->method('update')
            ->with()
            ->willReturn(
                new Task(
                    title: $dto->getTitle(),
                    description: new Description('Initial Task Description'),
                    owner: new User(
                        name: new Name('John Doe'),
                        email: new Email('ilovelaravel@gmail.com'),
                        password: new Password(password: 'P4sSw0rdSAD!@#)'),
                        role: Role::USER,
                        id: new Id('123e4567-e89b-12d3-a456-426614174000'),
                    ),
                    status: new Todo,
                    id: $this->taskExists->getId(),
                    createdAt: $this->taskExists->getCreatedAt(),
                )
            );

        $result = $this->updateTask->handle($dto);

        $this->assertEquals($this->taskExists->getId(), $result->getId());
        $this->assertEquals($dto->getTitle()->getValue(), $result->getTitle()->getValue());
        $this->assertEquals($this->taskExists->getDescription()->getValue(), $result->getDescription()->getValue());
        $this->assertEquals($this->taskExists->getStatus(), $result->getStatus());

        $this->assertNotEquals($this->taskExists->getTitle()->getValue(), $result->getTitle()->getValue());
    }

    #[Test]
    public function it_should_update_only_task_description(): void
    {
        $dto = new UpdateTaskDTO(
            id: $this->taskExists->getId(),
            description: new Description('Updated Task Description')
        );

        $this->findOrFailTaskByIdService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($dto->getId())
            ->willReturn($this->taskExists);

        $this->updateTaskService
            ->expects($this->once())
            ->method('update')
            ->with()
            ->willReturn(
                new Task(
                    title: $this->taskExists->getTitle(),
                    description: $dto->getDescription(),
                    owner: new User(
                        name: new Name('John Doe'),
                        email: new Email('ilovelaravel@gmail.com'),
                        password: new Password(password: 'P4sSw0rdSAD!@#)'),
                        role: Role::USER,
                        id: new Id('123e4567-e89b-12d3-a456-426614174000'),
                    ),
                    status: new Todo,
                    id: $this->taskExists->getId(),
                    createdAt: $this->taskExists->getCreatedAt(),
                )
            );

        $result = $this->updateTask->handle($dto);

        $this->assertEquals($this->taskExists->getId(), $result->getId());
        $this->assertEquals($this->taskExists->getTitle()->getValue(), $result->getTitle()->getValue());
        $this->assertEquals($dto->getDescription()->getValue(), $result->getDescription()->getValue());
        $this->assertEquals($this->taskExists->getStatus(), $result->getStatus());

        $this->assertNotEquals($this->taskExists->getDescription()->getValue(), $result->getDescription()->getValue());
    }

    #[Test]
    public function it_should_update_only_task_status(): void
    {
        $dto = new UpdateTaskDTO(
            id: $this->taskExists->getId(),
            status: Status::DONE
        );

        $this->findOrFailTaskByIdService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($dto->getId())
            ->willReturn($this->taskExists);

        $this->updateTaskService
            ->expects($this->once())
            ->method('update')
            ->with()
            ->willReturn(
                new Task(
                    title: $this->taskExists->getTitle(),
                    description: $this->taskExists->getDescription(),
                    owner: new User(
                        name: new Name('John Doe'),
                        email: new Email('ilovelaravel@gmail.com'),
                        password: new Password(password: 'P4sSw0rdSAD!@#)'),
                        role: Role::USER,
                        id: new Id('123e4567-e89b-12d3-a456-426614174000'),
                    ),
                    status: StatusFactory::map($dto->getStatus()),
                    id: $this->taskExists->getId(),
                    createdAt: $this->taskExists->getCreatedAt(),
                )
            );

        $result = $this->updateTask->handle($dto);

        $this->assertEquals($this->taskExists->getId(), $result->getId());
        $this->assertEquals($this->taskExists->getTitle()->getValue(), $result->getTitle()->getValue());
        $this->assertEquals($this->taskExists->getDescription()->getValue(), $result->getDescription()->getValue());
        $this->assertEquals($dto->getStatus(), $result->getStatus());

        $this->assertNotEquals($this->taskExists->getStatus(), $result->getStatus());
    }

    #[Test]
    public function it_should_update_entire_task_when_task_is_not_done_or_cancelled(): void
    {
        $dto = new UpdateTaskDTO(
            id: $this->taskExists->getId(),
            title: new Title('Updated Task Title'),
            description: new Description('Updated Task Description'),
            status: Status::DONE
        );

        $this->findOrFailTaskByIdService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($dto->getId())
            ->willReturn($this->taskExists);

        $this->updateTaskService
            ->expects($this->once())
            ->method('update')
            ->with()
            ->willReturn(
                new Task(
                    title: $dto->getTitle(),
                    description: $dto->getDescription(),
                    owner: new User(
                        name: new Name('John Doe'),
                        email: new Email('ilovelaravel@gmail.com'),
                        password: new Password(password: 'P4sSw0rdSAD!@#)'),
                        role: Role::USER,
                        id: new Id('123e4567-e89b-12d3-a456-426614174000'),
                    ),
                    status: StatusFactory::map($dto->getStatus()),
                    id: $this->taskExists->getId(),
                    createdAt: $this->taskExists->getCreatedAt(),
                )
            );

        $result = $this->updateTask->handle($dto);

        $this->assertEquals($this->taskExists->getId(), $result->getId());
        $this->assertEquals($dto->getTitle()->getValue(), $result->getTitle()->getValue());
        $this->assertEquals($dto->getDescription()->getValue(), $result->getDescription()->getValue());
        $this->assertEquals($dto->getStatus(), $result->getStatus());

        $this->assertNotEquals($this->taskExists->getTitle()->getValue(), $result->getTitle()->getValue());
        $this->assertNotEquals($this->taskExists->getDescription()->getValue(), $result->getDescription()->getValue());
        $this->assertNotEquals($this->taskExists->getStatus(), $result->getStatus());
    }
}
