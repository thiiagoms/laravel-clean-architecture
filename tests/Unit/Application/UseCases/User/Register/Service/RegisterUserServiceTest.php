<?php

namespace Tests\Unit\Application\UseCases\User\Register\Service;

use App\Application\UseCases\User\Common\Validator\VerifyUserEmailIsAvailable;
use App\Application\UseCases\User\Exception\EmailAlreadyExistsException;
use App\Application\UseCases\User\Register\Service\RegisterUserService;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\Factory\UserFactory;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Domain\Repository\User\Register\RegisterUserRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegisterUserServiceTest extends TestCase
{
    private VerifyUserEmailIsAvailable|MockObject $guardAgainstEmailAlreadyInUse;

    private RegisterUserRepositoryInterface|MockObject $repository;

    private User $user;

    private RegisterUserService $service;

    protected function setUp(): void
    {
        $this->guardAgainstEmailAlreadyInUse = $this->createMock(VerifyUserEmailIsAvailable::class);
        $this->repository = $this->createMock(RegisterUserRepositoryInterface::class);

        $this->user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SSw0ord!@#dASD_')
        );

        $this->service = new RegisterUserService(
            guardAgainstEmailAlreadyInUse: $this->guardAgainstEmailAlreadyInUse,
            repository: $this->repository
        );
    }

    #[Test]
    public function it_should_throw_exception_when_email_is_already_in_use(): void
    {
        $this->guardAgainstEmailAlreadyInUse
            ->expects($this->once())
            ->method('verify')
            ->with($this->user->getEmail())
            ->willThrowException(EmailAlreadyExistsException::create());

        $this->repository
            ->expects($this->never())
            ->method('save');

        $this->expectException(EmailAlreadyExistsException::class);
        $this->expectExceptionMessage('User with provided e-mail already exists');

        $this->service->create($this->user);
    }

    #[Test]
    public function it_should_create_user_successfully(): void
    {
        $this->guardAgainstEmailAlreadyInUse
            ->expects($this->once())
            ->method('verify')
            ->with($this->user->getEmail());

        $createdUser = new User(
            name: $this->user->getName(),
            email: $this->user->getEmail(),
            password: $this->user->getPassword(),
            role: $this->user->getRole(),
            id: new Id('123e4567-e89b-12d3-a456-426614174000'),
        );

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->user)
            ->willReturn($createdUser);

        $result = $this->service->create($this->user);

        $this->assertEquals($this->user->getName()->getValue(), $result->getName()->getValue());
        $this->assertEquals($this->user->getEmail()->getValue(), $result->getEmail()->getValue());
        $this->assertEquals($this->user->getPassword()->getValue(), $result->getPassword()->getValue());
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $result->getId()->getValue());
        $this->assertEquals('user', $result->getRole()->value);

        $this->assertNotNull($result->getCreatedAt());
        $this->assertNotNull($result->getUpdatedAt());
    }
}
