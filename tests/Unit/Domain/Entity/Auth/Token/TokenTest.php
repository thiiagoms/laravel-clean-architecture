<?php

namespace Tests\Unit\Domain\Entity\Auth\Token;

use App\Domain\Entity\Auth\Token\Factory\TokenFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    #[Test]
    public function it_should_create_token(): void
    {
        $token = TokenFactory::create(token: 'jwt.token.string', type: 'Bearer', expiresIn: 3600);

        $this->assertEquals('jwt.token.string', $token->getToken());
        $this->assertEquals('Bearer', $token->getType());
        $this->assertEquals(3600, $token->getExpiresIn());
    }

    #[Test]
    public function it_should_transform_token_to_array(): void
    {
        $token = TokenFactory::create(token: 'jwt.token.string', type: 'Bearer', expiresIn: 3600);

        $expectedData = [
            'token' => 'jwt.token.string',
            'type' => 'Bearer',
            'expiresIn' => 3600,
        ];

        $this->assertEquals($expectedData, $token->toArray());
    }

    #[Test]
    public function it_should_throw_exception_when_token_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Property 'token' cannot be empty.");

        TokenFactory::create(token: '', type: 'Bearer', expiresIn: 3600);
    }

    #[Test]
    public function it_should_throw_exception_when_type_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Property 'type' cannot be empty.");

        TokenFactory::create(token: 'jwt.token.string', type: '', expiresIn: 3600);
    }

    #[Test]
    public function it_should_throw_exception_when_expires_in_is_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Property 'expiresIn' cannot be empty.");

        TokenFactory::create(token: 'jwt.token.string', type: 'Bearer', expiresIn: 0);
    }
}
