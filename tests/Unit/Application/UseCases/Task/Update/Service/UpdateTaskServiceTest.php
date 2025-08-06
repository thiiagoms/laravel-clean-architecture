<?php

namespace Tests\Unit\Application\UseCases\Task\Update\Service;

use App\Application\UseCases\Task\Update\Service\UpdateTaskService;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Status\implementation\Doing;
use App\Domain\Entity\Task\Status\implementation\Todo;
use App\Domain\Entity\Task\Task;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Domain\Repository\Task\Update\UpdateTaskRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpdateTaskServiceTest extends TestCase
{
    private User $owner;

    private Task $createdTask;

    private UpdateTaskRepositoryInterface|MockObject $repository;

    private UpdateTaskService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->owner = new User(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password(password: 'P4sSw0rdSAD!@#)'),
            role: Role::USER,
            id: new Id('123e4567-e89b-12d3-a456-426614174000'),
        );

        $this->createdTask = new Task(
            title: new Title('Initial Task Title'),
            description: new Description('Initial Task Description'),
            owner: $this->owner,
            status: new Todo,
            id: new Id('123e4567-e89b-12d3-a456-426614174001'),
        );

        $this->repository = $this->createMock(UpdateTaskRepositoryInterface::class);

        $this->service = new UpdateTaskService($this->repository);
    }

    #[Test]
    public function it_should_update_task_title(): void
    {
        $taskToUpdate = new Task(
            title: new Title('Updated Task Title'),
            description: $this->createdTask->getDescription(),
            owner: $this->owner,
            status: new Todo,
            id: $this->createdTask->getId(),
            createdAt: $this->createdTask->getCreatedAt(),
            updatedAt: $this->createdTask->getUpdatedAt(),
        );

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($taskToUpdate)
            ->willReturn(
                new Task(
                    title: new Title('Updated Task Title'),
                    description: $this->createdTask->getDescription(),
                    owner: $this->owner,
                    status: new Todo,
                    id: $this->createdTask->getId(),
                    createdAt: $this->createdTask->getCreatedAt(),
                    updatedAt: $this->createdTask->getUpdatedAt(),
                )
            );

        $result = $this->service->update($taskToUpdate);

        $this->assertEquals($this->createdTask->getDescription(), $result->getDescription());
        $this->assertEquals($this->createdTask->getOwner(), $result->getOwner());
        $this->assertEquals($this->createdTask->getStatus(), $result->getStatus());
        $this->assertEquals($this->createdTask->getId(), $result->getId());

        $this->assertEquals('Updated Task Title', $result->getTitle()->getValue());

        $this->assertNotEquals($this->createdTask->getTitle(), $result->getTitle());
    }

    #[Test]
    public function it_should_update_task_description(): void
    {
        $taskToUpdate = new Task(
            title: $this->createdTask->getTitle(),
            description: new Description('Updated task description'),
            owner: $this->owner,
            status: new Todo,
            id: $this->createdTask->getId(),
            createdAt: $this->createdTask->getCreatedAt(),
            updatedAt: $this->createdTask->getUpdatedAt(),
        );

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($taskToUpdate)
            ->willReturn(
                new Task(
                    title: $this->createdTask->getTitle(),
                    description: new Description('Updated task description'),
                    owner: $this->owner,
                    status: new Todo,
                    id: $this->createdTask->getId(),
                    createdAt: $this->createdTask->getCreatedAt(),
                    updatedAt: $this->createdTask->getUpdatedAt(),
                )
            );

        $result = $this->service->update($taskToUpdate);

        $this->assertEquals($this->createdTask->getTitle(), $result->getTitle());
        $this->assertEquals($this->createdTask->getOwner(), $result->getOwner());
        $this->assertEquals($this->createdTask->getStatus(), $result->getStatus());
        $this->assertEquals($this->createdTask->getId(), $result->getId());

        $this->assertEquals('Updated task description', $result->getDescription()->getValue());

        $this->assertNotEquals($this->createdTask->getDescription(), $result->getDescription());
    }

    #[Test]
    public function it_should_update_task_status(): void
    {
        $taskToUpdate = new Task(
            title: $this->createdTask->getTitle(),
            description: $this->createdTask->getDescription(),
            owner: $this->owner,
            status: new Doing,
            id: $this->createdTask->getId(),
            createdAt: $this->createdTask->getCreatedAt(),
            updatedAt: $this->createdTask->getUpdatedAt(),
        );

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($taskToUpdate)
            ->willReturn(
                new Task(
                    title: $this->createdTask->getTitle(),
                    description: $this->createdTask->getDescription(),
                    owner: $this->owner,
                    status: new Doing,
                    id: $this->createdTask->getId(),
                    createdAt: $this->createdTask->getCreatedAt(),
                    updatedAt: $this->createdTask->getUpdatedAt(),
                )
            );

        $result = $this->service->update($taskToUpdate);

        $this->assertEquals($this->createdTask->getTitle(), $result->getTitle());
        $this->assertEquals($this->createdTask->getDescription(), $result->getDescription());
        $this->assertEquals($this->createdTask->getOwner(), $result->getOwner());
        $this->assertEquals($this->createdTask->getId(), $result->getId());

        $this->assertEquals('doing', $result->getStatus()->value);

        $this->assertNotEquals($this->createdTask->getStatus(), $result->getStatus());
    }
}
