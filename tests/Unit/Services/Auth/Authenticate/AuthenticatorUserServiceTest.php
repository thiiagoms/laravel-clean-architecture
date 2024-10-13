<?php

namespace Tests\Unit\Services\Auth\Authenticate;

use App\Messages\Auth\AuthMessage;
use App\Models\User;
use App\Services\Auth\Authenticate\AuthenticatorUserService;
use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;

class AuthenticatorUserServiceTest extends TestCase
{
    public function testItShouldThrowAuthenticationExceptionWithInvalidCredentialMessagesWhenUserDoesNotExists(): void
    {
        /** @var AuthenticatorUserService $authenticatorUserService */
        $authenticatorUserService = resolve(AuthenticatorUserService::class);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage(AuthMessage::INVALID_CREDENTIALS);

        $authenticatorUserService->handle(false, '');
    }

    public function testItShouldThrowAuthenticationExceptionWithInvalidCredentialMessagesWhenUserExistsButPasswordDoesNotMatch(): void
    {
        $userMock = new User(User::factory()->raw());

        /** @var AuthenticatorUserService $authenticatorUserService */
        $authenticatorUserService = resolve(AuthenticatorUserService::class);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage(AuthMessage::INVALID_CREDENTIALS);

        $authenticatorUserService->handle($userMock, 'password');
    }

    public function testItShouldThrowAnyExceptionWhenUserExistsAndUserPasswordMatch(): void
    {
        $this->expectNotToPerformAssertions();

        $userMock = new User(User::factory()->raw(['password' => 'password']));

        /** @var AuthenticatorUserService $authenticatorUserService */
        $authenticatorUserService = resolve(AuthenticatorUserService::class);

        $authenticatorUserService->handle($userMock, 'password');
    }
}
