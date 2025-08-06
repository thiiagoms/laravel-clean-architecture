<?php

namespace Tests\Feature\Presentation\Http\Api\V1\Auth\Authenticate;

use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    use DatabaseTransactions;

    private const string AUTHENTICATE_USER_ENDPOINT = '/api/v1/auth/login';

    public static function invalidEmailCases(): array
    {
        return [
            'email is required' => [
                '',
                fn (AssertableJson $json) => $json
                    ->hasAll(['error', 'error.email', 'error.email.0'])
                    ->whereAllType([
                        'error' => 'array',
                        'error.email' => 'array',
                        'error.email.0' => 'string',
                    ])
                    ->where('error.email.0', 'The provided email address is not valid. Please enter a valid email.'),
            ],
            'email is invalid' => [
                'invalid-name',
                fn (AssertableJson $json) => $json
                    ->hasAll(['error', 'error.email', 'error.email.0'])
                    ->whereAllType([
                        'error' => 'array',
                        'error.email' => 'array',
                        'error.email.0' => 'string',
                    ])
                    ->where('error.email.0', 'The provided email address is not valid. Please enter a valid email.'),
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidEmailCases')]
    public function it_should_validate_email(string $email, \Closure $response): void
    {
        $this
            ->postJson(self::AUTHENTICATE_USER_ENDPOINT, ['email' => $email, 'password' => '@p5sSw0rd!'])
            ->assertBadRequest()
            ->assertJson($response);
    }

    public static function invalidPasswordCases(): array
    {
        return [
            'should return password is required message when password value does not exists' => [
                '',
                fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.password',
                        'error.password.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.password' => 'array',
                        'error.password.0' => 'string',
                    ])
                    ->where(
                        'error.password.0',
                        'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.'
                    ),
            ],
            'should return password min length message when password is less than 8 characters' => [
                'p4sS!',
                fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.password',
                        'error.password.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.password' => 'array',
                        'error.password.0' => 'string',
                    ])
                    ->where(
                        'error.password.0',
                        'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.'
                    ),
            ],
            'should return password numbers message when password does not contain at least one number' => [
                'pAssssssssS!',
                fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.password',
                        'error.password.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.password' => 'array',
                        'error.password.0' => 'string',
                    ])
                    ->where(
                        'error.password.0',
                        'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.'
                    ),
            ],
            'should return password symbols message when password does not contain at least one symbol' => [
                'pAsssssss12sSD',
                fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.password',
                        'error.password.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.password' => 'array',
                        'error.password.0' => 'string',
                    ])
                    ->where(
                        'error.password.0',
                        'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.'
                    ),
            ],
            'should return password mixed case message when password does not contain at least one lower and upper case letter' => [
                'p4sssssss12s@ad',
                fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.password',
                        'error.password.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.password' => 'array',
                        'error.password.0' => 'string',
                    ])
                    ->where(
                        'error.password.0',
                        'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.'
                    ),
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidPasswordCases')]
    public function it_should_validate_password(string $password, \Closure $response): void
    {
        $payload = ['email' => fake()->freeEmail, 'password' => $password];

        $this
            ->postJson(self::AUTHENTICATE_USER_ENDPOINT, $payload)
            ->assertBadRequest()
            ->assertJson($response);
    }

    #[Test]
    public function it_should_return_unauthenticated_message_when_user_email_does_not_exists(): void
    {
        LaravelUserModel::factory()->createOne(['email' => 'ilovelaravel@gmail.com']);

        $this
            ->postJson(self::AUTHENTICATE_USER_ENDPOINT, [
                'email' => 'ilovephp@gmail.com',
                'password' => '@p5sSw0rd!',
            ])
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'Authentication failed. Please check your credentials.')
            );
    }

    #[Test]
    public function it_should_return_unauthenticated_message_when_user_password_does_not_match(): void
    {
        LaravelUserModel::factory()->createOne(['email' => 'ilovelaravel@gmail.com']);

        $this
            ->postJson(self::AUTHENTICATE_USER_ENDPOINT, [
                'email' => 'ilovelaravel@gmail.com',
                'password' => '@p5sSw0rd!',
            ])
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'Authentication failed. Please check your credentials.')
            );
    }

    #[Test]
    public function it_should_authenticate_user_and_return_token(): void
    {
        LaravelUserModel::factory()->createOne([
            'email' => 'ilovelaravel@gmail.com',
        ]);

        $this
            ->postJson(self::AUTHENTICATE_USER_ENDPOINT, [
                'email' => 'ilovelaravel@gmail.com',
                'password' => 'P4sSW0rd@!)_',
            ])
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->hasAll(['data', 'data.token', 'data.type', 'data.expiresIn'])
                ->whereAllType([
                    'data' => 'array',
                    'data.token' => 'string',
                    'data.type' => 'string',
                    'data.expiresIn' => 'integer',
                ])
                ->where('data.type', 'Bearer')
                ->where('data.expiresIn', 3600)
                ->etc()
            );
    }
}
