<?php

namespace Tests\Unit\Services\Auth\Token;

use App\DTO\Auth\Authenticate\AuthenticateUserDTO;
use App\DTO\Auth\Token\TokenDTO;
use App\Messages\Auth\AuthMessage;
use App\Models\User;
use App\Services\Auth\Token\TokenGeneratorService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenGeneratorServiceTest extends TestCase
{
    public function testItShoudlThrowAuthenticationEXceptionWithInvalidCredentialsMessageWhenUserCanNotAuthenticateWithCredentials(): void
    {
        $authDTO = AuthenticateUserDTO::fromArray(['email' => fake()->freeEmail(), 'password' => fake()->password()]);

        Auth::shouldReceive('guard')
            ->once()
            ->with('api')
            ->andReturnSelf();

        Auth::shouldReceive('attempt')
            ->once()
            ->with($authDTO->toArray())
            ->andReturnFalse();

        /** @var TokenGeneratorService $tokenGeneratorService */
        $tokenGeneratorService = resolve(TokenGeneratorService::class);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage(AuthMessage::INVALID_CREDENTIALS);

        $tokenGeneratorService->generateToken($authDTO);
    }

    public function testItShoudlReturnUserTokenWhenUserCanAuthenticateWithValidCredentials(): void
    {
        $userMock = new User(User::factory()->raw());

        $authDTO = AuthenticateUserDTO::fromArray(['email' => $userMock->email, 'password' => 'P4sSW0rd@!)_']);

        $tokenMock = JWTAuth::fromUser($userMock);

        Auth::shouldReceive('guard')
            ->once()
            ->with('api')
            ->andReturnSelf();

        Auth::shouldReceive('attempt')
            ->once()
            ->with($authDTO->toArray())
            ->andReturn($tokenMock);

        /** @var TokenGeneratorService $tokenGeneratorService */
        $tokenGeneratorService = resolve(TokenGeneratorService::class);

        $token = $tokenGeneratorService->generateToken($authDTO);

        $this->assertIsString($token);
        $this->assertEquals($tokenMock, $token);
    }

    public function testItShoudlReturnTokenDTOWhenReceiveAUsertoken(): void
    {
        $userMock = new User(User::factory()->raw());

        $tokenMock = JWTAuth::fromUser($userMock);

        Auth::shouldReceive('guard')
            ->once()
            ->with('api')
            ->andReturnSelf();

        Auth::shouldReceive('factory')
            ->once()
            ->andReturnSelf();

        Auth::shouldReceive('getTTL')
            ->once()
            ->andReturn(60);

        config()->set('jwt.ttl', 10);

        /** @var TokenGeneratorService $tokenGeneratorService */
        $tokenGeneratorService = resolve(TokenGeneratorService::class);

        /** @var TokenDTO $tokenDTO */
        $tokenDTO = $tokenGeneratorService->responseWithToken($tokenMock);

        $this->assertInstanceOf(TokenDTO::class, $tokenDTO);
        $this->assertEquals($tokenDTO->token, $tokenMock);
        $this->assertEquals($tokenDTO->type, 'bearer');
        $this->assertEquals($tokenDTO->expires_in, config('jwt.ttl') * 60);
    }
}
