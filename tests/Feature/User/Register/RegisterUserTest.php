<?php

namespace Tests\Feature\User\Register;

use App\Enums\User\UserNameEnum;
use App\Messages\User\UserEmailMessage;
use App\Messages\User\UserNameMessage;
use App\Messages\User\UserPasswordMessage;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use DatabaseTransactions;

    private const string REGISTER_USER_ENDPOINT = '/api/register';

    public static function validateUserNameProvider(): array
    {
        return [
            'should return that the name field is required when the value of the name field is empty' => [
                'name' => '',
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.name',
                        'error.name.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.name' => 'array',
                        'error.name.0' => 'string',
                    ])
                    ->where('error.name.0', UserNameMessage::nameIsRequired()),
            ],
            'should return that the name field is shorter than the allowed length when the value of the name field is less than the minimum' => [
                'name' => str_repeat('#', (UserNameEnum::MIN_LENGTH->value - 1)),
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.name',
                        'error.name.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.name' => 'array',
                        'error.name.0' => 'string',
                    ])
                    ->where('error.name.0', UserNameMessage::nameMinLength()),
            ],
            'should return that the name field is longer than the allowed length when the value of the name field is more than the maximum' => [
                'name' => implode(',', fake()->paragraphs(UserNameEnum::MAX_LENGTH->value)),
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.name',
                        'error.name.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.name' => 'array',
                        'error.name.0' => 'string',
                    ])
                    ->where('error.name.0', UserNameMessage::nameMaxLength()),
            ],
            'should return that the name field must be a string when the name field is not a valid string' => [
                'name' => fake()->randomFloat(),
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.name',
                        'error.name.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.name' => 'array',
                        'error.name.0' => 'string',
                    ])
                    ->where('error.name.0', UserNameMessage::nameMustBeString()),
            ],
        ];
    }

    #[DataProvider('validateUserNameProvider')]
    public function testValidateUserName(string|float $name, Closure $response): void
    {
        $data = ['name' => $name, 'email' => fake()->freeEmail(), 'password' => 'P4sSW0rd@!)_'];

        $this->postJson(self::REGISTER_USER_ENDPOINT, $data)
            ->assertBadRequest()
            ->assertJson($response);
    }

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
            'should return that the email field already exists when the email field already exists in the database' => [
                'email' => 'ilovelaravel@gmail.com',
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
                    ->where('error.email.0', UserEmailMessage::emailAlreadyExists()),
            ],
        ];
    }

    #[DataProvider('validateUserEmailProvider')]
    public function testValidateUserEmail(string $email, Closure $response): void
    {
        User::factory()->createOne(['email' => $email]);

        $data = ['name' => 'Jhon Doe', 'email' => $email, 'password' => 'P4sSW0rd@!)_'];

        $this->postJson(self::REGISTER_USER_ENDPOINT, $data)
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
        $data = ['name' => 'Jhon Doe', 'email' => fake()->freeEmail(), 'password' => $password];

        $this->postJson(self::REGISTER_USER_ENDPOINT, $data)
            ->assertBadRequest()
            ->assertJson($response);
    }

    public function testItShouldRegisterNewUserAndReturnCreatedUserData(): void
    {
        $data = [
            'name' => fake()->name(),
            'email' => fake()->freeEmail(),
            'password' => 'P4sSW0rd@!)_',
        ];

        $this->postJson(self::REGISTER_USER_ENDPOINT, $data)
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->hasAll([
                    'data',
                    'data.id',
                    'data.name',
                    'data.email',
                    'data.role',
                    'data.created_at',
                    'data.updated_at',
                ])
                ->whereAllType([
                    'data' => 'array',
                    'data.id' => 'string',
                    'data.name' => 'string',
                    'data.email' => 'string',
                    'data.role' => 'string',
                    'data.created_at' => 'string',
                    'data.updated_at' => 'string',
                ])
                ->whereAll([
                    'data.name' => $data['name'],
                    'data.email' => $data['email'],
                    'data.role' => 'user',
                ])
            );
    }
}
