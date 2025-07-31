<?php

namespace Tests\Unit\Application\UseCases\User\Common\Service;

use App\Application\UseCases\User\Common\Service\FindOrFailUserByEmailService;
use App\Application\UseCases\User\Exception\UserNotFoundException;
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

class FindOrFailUserByEmailServiceTest extends TestCase
{
    private Email $email;

    private FindUserByEmailRepositoryInterface|MockObject $repository;

    private FindOrFailUserByEmailService $service;

    /**
     * @throws \Exception|Exception
     */
    protected function setUp(): void
    {
        $this->email = new Email('ilovelaravel@gmail.com');

        $this->repository = $this->createMock(FindUserByEmailRepositoryInterface::class);

        $this->service = new FindOrFailUserByEmailService($this->repository);
    }

    #[Test]
    public function itShouldThrowExceptionWhenUserNotFound(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($this->email)
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found.');

        $this->service->findOrFail($this->email);
    }

    #[Test]
    public function itShouldReturnUserWhenFound(): void
    {
        $user = new User(
            name: new Name('John Doe'),
            email: $this->email,
            password: new Password('P4SsW0rd!@#D_'),
            role: Role::USER,
            id: new Id(fake()->uuid())
        );

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($this->email)
            ->willReturn($user);

        $result = $this->service->findOrFail($this->email);

        $this->assertEquals($user, $result);
    }
}
