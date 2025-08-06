<?php

namespace Tests\Unit\Application\UseCases\User\Common\Service;

use App\Application\UseCases\User\Common\Service\FindOrFailUserByIdService;
use App\Application\UseCases\User\Exception\UserNotFoundException;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Domain\Repository\User\Find\FindUserByIdRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FindOrFailUserByIdServiceTest extends TestCase
{
    private Id $id;

    private FindUserByIdRepositoryInterface|MockObject $repository;

    private FindOrFailUserByIdService $service;

    /**
     * @throws \Exception|Exception
     */
    protected function setUp(): void
    {
        $this->id = new Id(fake()->uuid());

        $this->repository = $this->createMock(FindUserByIdRepositoryInterface::class);

        $this->service = new FindOrFailUserByIdService($this->repository);
    }

    #[Test]
    public function it_should_throw_exception_when_user_not_found(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($this->id)
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found.');

        $this->service->findOrFail($this->id);
    }

    #[Test]
    public function it_should_return_user_when_found(): void
    {
        $user = new User(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#D_'),
            role: Role::USER,
            id: $this->id
        );

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($this->id)
            ->willReturn($user);

        $result = $this->service->findOrFail($this->id);

        $this->assertEquals($user, $result);
    }
}
