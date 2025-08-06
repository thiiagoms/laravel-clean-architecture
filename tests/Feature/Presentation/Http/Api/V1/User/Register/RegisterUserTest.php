<?php

namespace Tests\Feature\Presentation\Http\Api\V1\User\Register;

use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use DatabaseTransactions;

    private const string REGISTER_USER_ENDPOINT = '/api/v1/register';

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public static function invalidNameCases(): array
    {
        return [
            'should return name is required message when name value does not exist' => [
                '',
                fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.name.0', 'Name must be between 3 and 150 characters and contains only letters.'),
            ],
            'should return name min length message when name is lower than min length' => [
                str_repeat('#', 2),
                fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.name.0', 'Name must be between 3 and 150 characters and contains only letters.'),
            ],
            'should return name max length message when name length is higher than max length' => [
                implode(',', fake()->paragraphs(151)),
                fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.name.0', 'Name must be between 3 and 150 characters and contains only letters.'),
            ],
            'should return name type message when name is not a string' => [
                fake()->randomFloat(),
                fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.name.0', 'Name must be between 3 and 150 characters and contains only letters.'),
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidNameCases')]
    public function it_should_validate_name(string|float $name, \Closure $response): void
    {
        $payload = ['name' => $name, 'email' => fake()->freeEmail(), 'password' => '@p5sSw0rd!'];

        $this
            ->postJson(self::REGISTER_USER_ENDPOINT, $payload)
            ->assertBadRequest()
            ->assertJson($response);
    }

    public static function invalidEmailCases(): array
    {
        return [
            'should return email is required message when email value does not exists' => [
                '',
                fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.email.0', 'The provided email address is not valid. Please enter a valid email.'),
            ],
            'should return email is invalid message when email is not a valid email' => [
                fake()->name(),
                fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.email.0', 'The provided email address is not valid. Please enter a valid email.'),
            ],
            'should return email already exists message when email already exists' => [
                'ilovelaravel@gmail.com',
                fn (AssertableJson $json): AssertableJson => $json
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
                    ->where('error.email.0', 'The email has already been taken.'),
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidEmailCases')]
    public function it_should_validate_email(string $email, \Closure $response): void
    {
        LaravelUserModel::factory()->createOne(['email' => 'ilovelaravel@gmail.com']);

        $payload = ['name' => fake()->name(), 'email' => $email, 'password' => '@p5sSw0rd!'];

        $this
            ->postJson(self::REGISTER_USER_ENDPOINT, $payload)
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
        $payload = ['name' => fake()->name(), 'email' => fake()->freeEmail, 'password' => $password];

        $this
            ->postJson(self::REGISTER_USER_ENDPOINT, $payload)
            ->assertBadRequest()
            ->assertJson($response);
    }

    #[Test]
    public function it_should_create_user_and_return_created_user_data(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => fake()->freeEmail(),
            'password' => '@p5sSw0rd!',
        ];

        $this
            ->postJson(self::REGISTER_USER_ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->hasAll([
                    'data',
                    'data.id',
                    'data.name',
                    'data.email',
                    'data.created_at',
                    'data.updated_at',
                ])
                ->whereAllType([
                    'data' => 'array',
                    'data.id' => 'string',
                    'data.name' => 'string',
                    'data.email' => 'string',
                    'data.created_at' => 'string',
                    'data.updated_at' => 'string',
                ])
                ->whereAll([
                    'data.name' => $payload['name'],
                    'data.email' => $payload['email'],
                ]));
    }
}
