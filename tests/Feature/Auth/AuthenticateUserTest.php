<?php

namespace Tests\Feature\Auth;

use App\Messages\Auth\AuthMessage;
use App\Messages\User\UserEmailMessage;
use App\Messages\User\UserPasswordMessage;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticateUserTest extends TestCase
{
    use DatabaseTransactions;

    private const string AUTH_USER_ENDPOINT = '/api/auth';

    public static function validateUserEmailProvider(): array
    {
        return [
            'should return that the email field is required when the value of the email field is empty' => [
                'email' => '',
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.email',
                        'error.email.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.email' => 'array',
                        'error.email.0' => 'string',
                    ])
                    ->where('error.email.0', UserEmailMessage::emailIsRequired()),
            ],
            'should return that the email field is invalid when the value of the email field is not a valid email' => [
                'email' => fake()->name(),
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.email',
                        'error.email.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.email' => 'array',
                        'error.email.0' => 'string',
                    ])
                    ->where('error.email.0', UserEmailMessage::emailIsInvalid()),
            ],
        ];
    }

    #[DataProvider('validateUserEmailProvider')]
    public function testValidateUserEmail(string $email, Closure $response): void
    {
        $data = ['email' => $email, 'password' => 'P4sSW0rd@!)_'];

        $this
            ->postJson(self::AUTH_USER_ENDPOINT, $data)
            ->assertBadRequest()
            ->assertJson($response);
    }

    public static function validateUserPassword(): array
    {
        return [
            'should return that the password field is required when the value of the password field is empty' => [
                'password' => '',
                'response' => fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.password.0', UserPasswordMessage::passwordIsRequired()),
            ],
            'should return that the password field is shorter than the min length when the value of the password field is shorter than the min length' => [
                'password' => 'p4sS',
                'response' => fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.password.0', UserPasswordMessage::passwordMinLength()),
            ],
            'should return that the password field does not contain numbers when the value of the password field does not contain numbers' => [
                'password' => 'pAsss@!sssssS',
                'response' => fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.password.0', UserPasswordMessage::passwordNumbers()),
            ],
            'should return that the password field does not contain symbols when the value of the password field does not contain symbols' => [
                'password' => 'pAsssssss12sSD',
                'response' => fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.password.0', UserPasswordMessage::passwordSymbols()),
            ],
            'should return that the password field does not contain mixed case when the value of the password field does not contain mixed case' => [
                'password' => 'p4sssssss12s@ad',
                'response' => fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.password.0', UserPasswordMessage::passwordMixedCase()),
            ],
        ];
    }

    #[DataProvider('validateUserPassword')]
    public function testValidateUserPassword(string $password, Closure $response): void
    {
        $data = ['email' => fake()->freeEmail(), 'password' => $password];

        $this
            ->postJson(self::AUTH_USER_ENDPOINT, $data)
            ->assertBadRequest()
            ->assertJson($response);
    }

    public function testItShouldReturnInvalidCredentialsMessageWhenEmailProvidedIsValidButDoesNotExists(): void
    {
        $data = ['email' => fake()->freeEmail(), 'password' => 'p4SWo$ad12_'];

        $this
            ->postJson(self::AUTH_USER_ENDPOINT, $data)
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('message')
                ->whereType('message', 'string')
                ->where('message', AuthMessage::INVALID_CREDENTIALS)
            );
    }

    public function testItShouldReturnInvalidCredentialsMessageWhenUserEmailExistsButPasswordIsInvalid(): void
    {
        User::factory()->createOne(['email' => 'ilovelaravel@gmail.com']);

        $data = ['email' => 'ilovelaravel@gmail.com', 'password' => 'p4SWo$ad12_'];

        $this
            ->postJson(self::AUTH_USER_ENDPOINT, $data)
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('message')
                ->whereType('message', 'string')
                ->where('message', AuthMessage::INVALID_CREDENTIALS)
            );
    }

    public function testItShouldReturnUserTokenWhenCredentialsProvidedMatchWithUserInDatabase(): void
    {
        User::factory()->createOne(['email' => 'ilovelaravel@gmail.com', 'password' => 'p4SWo$ad12_']);

        $data = ['email' => 'ilovelaravel@gmail.com', 'password' => 'p4SWo$ad12_'];

        $this
            ->postJson(self::AUTH_USER_ENDPOINT, $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->hasAll([
                    'data',
                    'data.token',
                    'data.type',
                    'data.expires_in',
                ])
                ->whereAllType([
                    'data' => 'array',
                    'data.token' => 'string',
                    'data.type' => 'string',
                    'data.expires_in' => 'integer',
                ])
            );
    }
}
