<?php

namespace Tests\Unit\Application\UseCases\User\Common\Validator;

use App\Application\UseCases\User\Common\Validator\VerifyUserEmailIsAvailable;
use App\Application\UseCases\User\Exception\EmailAlreadyExistsException;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Domain\Repository\User\Find\FindUserByEmailRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class VerifyUserEmailIsAvailableTest extends TestCase
{
    private Email $email;

    private FindUserByEmailRepositoryInterface|MockObject $repository;

    private VerifyUserEmailIsAvailable $verifyUserEmailIsAvailable;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->email = new Email('ilovelaravel@gmail.com');

        $this->repository = $this->createMock(FindUserByEmailRepositoryInterface::class);

        $this->verifyUserEmailIsAvailable = new VerifyUserEmailIsAvailable($this->repository);
    }

    #[Test]
    public function itShouldReturnUserWhenEmailIsAvailable(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($this->email)
            ->willReturn(null);

        $this->verifyUserEmailIsAvailable->verify($this->email);

        $this->assertTrue(true);
    }

    #[Test]
    public function itShouldThrowEmailAlreadyExistsExceptionWhenEmailIsNotAvailable(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($this->email)
            ->willReturn(new User(
                name: new Name('John Doe'),
                email: $this->email,
                password: new Password('P4SSw0ord!@#dASD_'),
                role: Role::USER,
                id: new Id(fake()->uuid())
            ));

        $this->expectException(EmailAlreadyExistsException::class);
        $this->expectExceptionMessage('User with provided e-mail already exists');

        $this->verifyUserEmailIsAvailable->verify($this->email);
    }
}
