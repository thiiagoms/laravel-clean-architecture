<?php

namespace Tests\Unit\Auth\Authenticate;

use App\Contracts\Services\Auth\Token\TokenGeneratorServiceContract;
use App\Contracts\Services\User\Find\FindUserByEmailServiceContract;
use App\DTO\Auth\Authenticate\AuthenticateUserDTO;
use App\DTO\Auth\Token\TokenDTO;
use App\Exceptions\LogicalException;
use App\Messages\Auth\AuthMessage;
use App\Messages\User\UserEmailMessage;
use App\Models\User;
use App\Services\Auth\Authenticate\AuthenticateUserService;
use Illuminate\Auth\AuthenticationException;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateUserServiceTest extends TestCase
{
    public function testItShouldThrowLogicalExceptionWithInvalidEmailMessageWhenEmailProvidedIsInvalid(): void
    {
        $authDTO = AuthenticateUserDTO::fromArray(['email' => fake()->name(), 'password' => fake()->password()]);

        /** @var AuthenticateUserService $authenticateUserService */
        $authenticateUserService = resolve(AuthenticateUserService::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(UserEmailMessage::emailIsInvalid());

        $authenticateUserService->handle($authDTO);
    }

    public function testItShouldThrowAuthenticationExceptionWithInvalidCredentialMessagesWhenUserEmailDoesNotExists(): void
    {
        $authDTO = AuthenticateUserDTO::fromArray(['email' => fake()->freeEmail(), 'password' => fake()->password()]);

        $findUserByEmailServiceMock = Mockery::mock(FindUserByEmailServiceContract::class);

        $findUserByEmailServiceMock->shouldReceive('handle')
            ->once()
            ->with($authDTO->email)
            ->andReturnFalse();

        /** @var AuthenticateUserService $authenticateUserService */
        $authenticateUserService = resolve(AuthenticateUserService::class, [
            'findUserByEmailService' => $findUserByEmailServiceMock,
        ]);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage(AuthMessage::INVALID_CREDENTIALS);

        $authenticateUserService->handle($authDTO);

        Mockery::close();
    }

    public function testItShouldThrowAuthenticationExceptionWithInvalidCredentialMessagesWhenUserExistsButPasswordDoesNotMatch(): void
    {
        $authDTO = AuthenticateUserDTO::fromArray(['email' => fake()->freeEmail(), 'password' => fake()->password()]);

        $userWithEmailMock = new User(User::factory()->raw(['email' => $authDTO->email]));

        $findUserByEmailServiceMock = Mockery::mock(FindUserByEmailServiceContract::class);

        $findUserByEmailServiceMock->shouldReceive('handle')
            ->once()
            ->with($authDTO->email)
            ->andReturn($userWithEmailMock);

        /** @var AuthenticateUserService $authenticateUserService */
        $authenticateUserService = resolve(AuthenticateUserService::class, [
            'findUserByEmailService' => $findUserByEmailServiceMock,
        ]);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage(AuthMessage::INVALID_CREDENTIALS);

        $authenticateUserService->handle($authDTO);

        Mockery::close();
    }

    public function testItShouldReturnUserTokenDTOWithTokenSettingsWhenUserCredentialsMatchWithUserInDatabase(): void
    {
        $userMock = new User(User::factory()->raw());

        $authDTO = AuthenticateUserDTO::fromArray(['email' => $userMock->email, 'password' => 'P4sSW0rd@!)_']);

        $findUserByEmailServiceMock = Mockery::mock(FindUserByEmailServiceContract::class);

        $findUserByEmailServiceMock->shouldReceive('handle')
            ->once()
            ->with($authDTO->email)
            ->andReturn($userMock);

        $tokenMock = JWTAuth::fromUser($userMock);

        $tokenGeneratorServiceMock = Mockery::mock(TokenGeneratorServiceContract::class);

        $tokenGeneratorServiceMock->shouldReceive('generateToken')
            ->once()
            ->with($authDTO)
            ->andReturn($tokenMock);

        $tokenGeneratorServiceMock->shouldReceive('responseWithToken')
            ->once()
            ->with($tokenMock)
            ->andReturn(TokenDTO::fromArray([
                'token' => $tokenMock,
                'type' => 'bearer',
                'expires_in' => 60 * 60,
            ]));

        /** @var AuthenticateUserService $authenticateUserService */
        $authenticateUserService = resolve(AuthenticateUserService::class, [
            'findUserByEmailService' => $findUserByEmailServiceMock,
            'tokenGeneratorService' => $tokenGeneratorServiceMock,
        ]);

        /** @var TokenDTO $tokenDTO */
        $tokenDTO = $authenticateUserService->handle($authDTO);

        $this->assertInstanceOf(TokenDTO::class, $tokenDTO);
        $this->assertEquals($tokenDTO->token, $tokenMock);
        $this->assertEquals($tokenDTO->type, 'bearer');
        $this->assertEquals($tokenDTO->expires_in, 3600);

        Mockery::close();
    }
}
