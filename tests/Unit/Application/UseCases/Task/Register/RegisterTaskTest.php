<?php

namespace Tests\Unit\Application\UseCases\Task\Register;

use App\Application\UseCases\Task\Register\DTO\RegisterTaskDTO;
use App\Application\UseCases\Task\Register\RegisterTask;
use App\Application\UseCases\Task\Register\Service\RegisterTaskService;
use App\Application\UseCases\User\Common\Service\FindOrFailUserByIdService;
use App\Application\UseCases\User\Exception\UserNotFoundException;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Status\implementation\Todo;
use App\Domain\Entity\Task\Task;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegisterTaskTest extends TestCase
{
    private RegisterTaskDTO $dto;

    private RegisterTaskService|MockObject $registerTaskService;

    private FindOrFailUserByIdService $findOrFailUserByIdService;

    private RegisterTask $registerTask;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->dto = new RegisterTaskDTO(
            title: new Title('Task title test'),
            description: new Description('Task description test'),
            userId: new Id('736755f2-339a-4e65-bf52-bf6de04ba524')
        );

        $this->findOrFailUserByIdService = $this->createMock(FindOrFailUserByIdService::class);

        $this->registerTaskService = $this->createMock(RegisterTaskService::class);

        $this->registerTask = new RegisterTask(
            registerTaskService: $this->registerTaskService,
            findOrFailUserByIdService: $this->findOrFailUserByIdService
        );
    }

    #[Test]
    public function it_should_throw_exception_when_user_not_found(): void
    {
        $this->findOrFailUserByIdService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($this->dto->getUserId())
            ->willThrowException(UserNotFoundException::create());

        $this->registerTaskService
            ->expects($this->never())
            ->method('handle');

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $this->registerTask->handle($this->dto);
    }

    #[Test]
    public function it_should_register_task_successfully(): void
    {
        $user = new User(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password(password: 'P4SsW0rD!123'),
            role: Role::USER,
            id: $this->dto->getUserId()
        );

        $this->findOrFailUserByIdService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($this->dto->getUserId())
            ->willReturn($user);

        $this->registerTaskService
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (Task $task) use ($user): bool {
                return $task->getTitle() === $this->dto->getTitle()
                    && $task->getDescription() === $this->dto->getDescription()
                    && $task->getOwner()->getId() === $user->getId();
            }))
            ->willReturn(
                new Task(
                    title: $this->dto->getTitle(),
                    description: $this->dto->getDescription(),
                    owner: $user,
                    status: new Todo,
                    id: new Id(fake()->uuid()),
                    createdAt: new \DateTimeImmutable,
                    updatedAt: new \DateTimeImmutable
                )
            );

        $result = $this->registerTask->handle($this->dto);

        $this->assertEquals($this->dto->getTitle(), $result->getTitle());
        $this->assertEquals($this->dto->getDescription(), $result->getDescription());
    }
}
