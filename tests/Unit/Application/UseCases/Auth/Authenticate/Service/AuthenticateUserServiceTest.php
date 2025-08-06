<?php

namespace Tests\Unit\Application\UseCases\Auth\Authenticate\Service;

use App\Application\UseCases\Auth\Authenticate\DTO\AuthenticateDTO;
use App\Application\UseCases\Auth\Authenticate\Service\AuthenticateUserService;
use App\Application\UseCases\Auth\Authenticate\Service\CanAuthenticateUserService;
use App\Application\UseCases\User\Common\Service\FindOrFailUserByEmailService;
use App\Application\UseCases\User\Exception\UserNotFoundException;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthenticateUserServiceTest extends TestCase
{
    private FindOrFailUserByEmailService|MockObject $findOrFailUserByEmailService;

    private AuthenticateUserService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->findOrFailUserByEmailService = $this->createMock(FindOrFailUserByEmailService::class);

        $this->service = new AuthenticateUserService(
            findOrFailUserByEmail: $this->findOrFailUserByEmailService,
            canAuthenticateUserService: new CanAuthenticateUserService
        );
    }

    #[Test]
    public function it_should_return_user_when_user_found_and_password_matches(): void
    {
        $email = new Email('ilovelaravel@gmail.com');
        $password = new Password(password: 'P4SSw0ord!@#dASD_', hashed: false);

        $dto = new AuthenticateDTO(email: $email, password: $password);

        $existsUser = new User(
            name: new Name('John Doe'),
            email: $email,
            password: new Password(password: 'P4SSw0ord!@#dASD_'),
            role: Role::USER,
            id: new Id('123e4567-e89b-12d3-a456-426614174000'),
        );

        $this->findOrFailUserByEmailService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($dto->getEmail())
            ->willReturn($existsUser);

        $result = $this->service->authenticate($dto);

        $this->assertEquals($existsUser, $result);
    }

    #[Test]
    public function it_should_return_null_when_user_not_found(): void
    {
        $dto = new AuthenticateDTO(
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password(password: 'P4SSw0ord!@#dASD_', hashed: false)
        );

        $this->findOrFailUserByEmailService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($dto->getEmail())
            ->willThrowException(UserNotFoundException::create());

        $result = $this->service->authenticate($dto);

        $this->assertNull($result);
    }

    #[Test]
    public function it_should_return_null_when_user_password_does_not_match(): void
    {
        $email = new Email('ilovelaravel@gmail.com');
        $password = new Password(password: 'P4SSw0ord!@#dASD_', hashed: false);

        $dto = new AuthenticateDTO(email: $email, password: $password);

        $existsUser = new User(
            name: new Name('John Doe'),
            email: $email,
            password: new Password(password: 'P4SSw0ord@@@#ZXCVb_'),
            role: Role::USER,
            id: new Id('123e4567-e89b-12d3-a456-426614174000'),
        );

        $this->findOrFailUserByEmailService
            ->expects($this->once())
            ->method('findOrFail')
            ->with($dto->getEmail())
            ->willReturn($existsUser);

        $result = $this->service->authenticate($dto);

        $this->assertNull($result);
    }
}
