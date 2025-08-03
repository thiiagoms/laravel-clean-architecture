<?php

namespace Tests\Feature\Presentation\Http\Api\V1\Task\Register;

use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterTaskTest extends TestCase
{
    use DatabaseTransactions;

    private const string REGISTER_TASK_ENDPOINT = '/api/v1/task/register';

    #[Test]
    public function itShouldNotAllowRegisterTaskWhenUserIsNotAuthenticated(): void
    {
        $this
            ->postJson(self::REGISTER_TASK_ENDPOINT, [
                'title' => 'Test Task', 'description' => 'This is a test task',
            ])
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'Unauthenticated.')
            );
    }

    public static function invalidTitleCases(): array
    {
        return [
            'should return that the title field is required when the value of the title field is empty' => [
                'title' => '',
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.title',
                        'error.title.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.title' => 'array',
                        'error.title.0' => 'string',
                    ])
                    ->where(
                        'error.title.0',
                        'The title field is required.'
                    ),
            ],
            'should return that the title field is longer than the allowed length when the value of the title field is more than the maximum' => [
                'title' => implode(',', fake()->paragraphs(101)),
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.title',
                        'error.title.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.title' => 'array',
                        'error.title.0' => 'string',
                    ])
                    ->where(
                        'error.title.0',
                        'The title field must not be greater than 100 characters.'
                    ),
            ],
            'should return that the title field must be a string when the title field is not a valid string' => [
                'title' => 123,
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.title',
                        'error.title.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.title' => 'array',
                        'error.title.0' => 'string',
                    ])
                    ->where(
                        'error.title.0',
                        'The title field must be a string.'
                    ),
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidTitleCases')]
    public function itShouldValidateTitle(string|int $title, \Closure $response): void
    {
        $user = LaravelUserModel::factory()->createOne();

        auth('api')->attempt(['email' => $user->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($user)
            ->postJson(self::REGISTER_TASK_ENDPOINT, [
                'title' => $title,
                'description' => 'This is a test task',
            ])
            ->assertJson($response);
    }

    public static function invalidDescriptionCases(): array
    {
        return [
            'should return that the description field is required when the value of the description field is empty' => [
                'description' => '',
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.description',
                        'error.description.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.description' => 'array',
                        'error.description.0' => 'string',
                    ])
                    ->where('error.description.0', 'The description field is required.'),
            ],
            'should return that the description field must be a string when the description field is not a valid string' => [
                'description' => 123,
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.description',
                        'error.description.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.description' => 'array',
                        'error.description.0' => 'string',
                    ])
                    ->where('error.description.0', 'The description field must be a string.'),
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidDescriptionCases')]
    public function itShouldValidateDescription(string|int $description, \Closure $response): void
    {
        $user = LaravelUserModel::factory()->createOne();

        auth('api')->attempt(['email' => $user->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($user)
            ->postJson(self::REGISTER_TASK_ENDPOINT, [
                'title' => 'Task title example',
                'description' => $description,
            ])
            ->assertJson($response);
    }

    #[Test]
    public function itShouldCreateNewTaskForAuthenticatedUser(): void
    {
        $user = LaravelUserModel::factory()->createOne();

        auth('api')->attempt(['email' => $user->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($user)
            ->postJson(self::REGISTER_TASK_ENDPOINT, [
                'title' => 'Task title example',
                'description' => 'This is a test task',
            ])
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->hasAll([
                    'data',
                    'data.id',
                    'data.title',
                    'data.description',
                    'data.status',
                    'data.created_at',
                    'data.updated_at',
                ])
                ->whereAllType([
                    'data' => 'array',
                    'data.id' => 'string',
                    'data.title' => 'string',
                    'data.description' => 'string',
                    'data.status' => 'string',
                    'data.created_at' => 'string',
                    'data.updated_at' => 'string',
                ])
                ->where('data.title', 'Task title example')
                ->where('data.description', 'This is a test task')
                ->where('data.status', 'todo')
            );

    }
}
