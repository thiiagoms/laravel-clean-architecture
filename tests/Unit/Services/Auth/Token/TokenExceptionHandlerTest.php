<?php

namespace Tests\Unit\Services\Auth\Token;

use App\Messages\Auth\AuthMessage;
use App\Services\Auth\Token\TokenExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;

class TokenExceptionHandlerTest extends TestCase
{
    public function testItShouldReturnTokeWhenTokenIsString(): void
    {
        $token = false;

        /** @var TokenExceptionHandler $tokenExceptionHandler */
        $tokenExceptionHandler = resolve(TokenExceptionHandler::class);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage(AuthMessage::INVALID_CREDENTIALS);

        $tokenExceptionHandler->handle($token);
    }

    public function testItShoudlThrowAuthenticationEXceptionWithInvalidCredentialsMessageWhenTokenIsNotATokenString(): void
    {
        $token = 'token';

        /** @var TokenExceptionHandler $tokenExceptionHandler */
        $tokenExceptionHandler = resolve(TokenExceptionHandler::class);

        $result = $tokenExceptionHandler->handle($token);

        $this->assertIsString($result);
        $this->assertEquals($token, $token);
    }
}
