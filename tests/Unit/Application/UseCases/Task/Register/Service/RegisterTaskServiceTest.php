<?php

namespace Tests\Unit\Application\UseCases\Task\Register\Service;

use App\Application\UseCases\Task\Register\Service\RegisterTaskService;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Factory\TaskFactory;
use App\Domain\Entity\Task\Status\implementation\Todo;
use App\Domain\Entity\Task\Task;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Domain\Entity\User\Factory\UserFactory;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Domain\Repository\Task\Register\RegisterTaskRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RegisterTaskServiceTest extends TestCase
{
    private RegisterTaskRepositoryInterface $repository;

    private RegisterTaskService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RegisterTaskRepositoryInterface::class);

        $this->service = new RegisterTaskService($this->repository);
    }

    #[Test]
    public function it_should_register_task(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password(password: 'P4SsW0rD!123')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user
        );

        $createdTask = new Task(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
            status: new Todo,
            id: new Id(fake()->uuid())
        );

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($task)
            ->willReturn($createdTask);

        $result = $this->service->handle($task);

        $this->assertEquals($user->getEmail()->getValue(), $result->getOwner()->getEmail()->getValue());
        $this->assertEquals($user->getEmail()->getValue(), $result->getOwner()->getEmail()->getValue());

        $this->assertEquals($task->getTitle()->getValue(), $result->getTitle()->getValue());
        $this->assertEquals($task->getDescription()->getValue(), $result->getDescription()->getValue());
        $this->assertTrue($result->getStatus()->isToDo());

        $this->assertNotNull($result->getId());
    }
}
