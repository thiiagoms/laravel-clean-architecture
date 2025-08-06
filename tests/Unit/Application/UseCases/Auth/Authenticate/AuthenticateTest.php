<?php

namespace Tests\Unit\Application\UseCases\Auth\Authenticate;

use App\Application\UseCases\Auth\Authenticate\Authenticate;
use App\Application\UseCases\Auth\Authenticate\DTO\AuthenticateDTO;
use App\Application\UseCases\Auth\Authenticate\Service\AuthenticateUserService;
use App\Application\UseCases\Auth\Common\Interface\GenerateTokenInterface;
use App\Application\UseCases\Auth\Exception\InvalidCredentialsException;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Auth\Token\Factory\TokenFactory;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthenticateTest extends TestCase
{
    private AuthenticateDTO $dto;

    private AuthenticateUserService|MockObject $authenticateUserService;

    private GenerateTokenInterface|MockObject $generateToken;

    private Authenticate $authenticate;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->dto = new AuthenticateDTO(
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password(password: 'P4SSw0ord!@#dASD_', hashed: false)
        );

        $this->authenticateUserService = $this->createMock(AuthenticateUserService::class);

        $this->generateToken = $this->createMock(GenerateTokenInterface::class);

        $this->authenticate = new Authenticate(
            service: $this->authenticateUserService,
            generateToken: $this->generateToken
        );
    }

    #[Test]
    public function it_should_throw_exception_when_authentication_fails(): void
    {
        $this->authenticateUserService
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->dto)
            ->willReturn(null);

        $this->generateToken
            ->expects($this->never())
            ->method('create');

        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage('Authentication failed. Please check your credentials.');

        $this->authenticate->handle($this->dto);
    }

    #[Test]
    public function it_should_return_token_when_authentication_succeeds(): void
    {
        $existsUser = new User(
            name: new Name('John Doe'),
            email: $this->dto->getEmail(),
            password: new Password(password: 'P4SSw0ord!@#dASD_'),
            role: Role::USER,
            id: new Id('123e4567-e89b-12d3-a456-426614174000'),
        );

        $this->authenticateUserService
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->dto)
            ->willReturn($existsUser);

        $this->generateToken
            ->expects($this->once())
            ->method('create')
            ->with($existsUser)
            ->willReturn(TokenFactory::create(
                token: 'valid_token_123',
                type: 'Bearer',
                expiresIn: 3600
            ));

        $result = $this->authenticate->handle($this->dto);

        $this->assertEquals('valid_token_123', $result->getToken());
        $this->assertEquals('Bearer', $result->getType());
        $this->assertEquals(3600, $result->getExpiresIn());
    }
}
