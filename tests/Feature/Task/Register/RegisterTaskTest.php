<?php

namespace Tests\Feature\Task\Register;

use App\Enums\Task\TaskTitleEnum;
use App\Infrastructure\Persistence\Model\User;
use App\Messages\Auth\AuthMessage;
use App\Messages\Task\TaskDescriptionMessage;
use App\Messages\Task\TaskStatusMessage;
use App\Messages\Task\TaskTitleMessage;
use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegisterTaskTest extends TestCase
{
    use DatabaseTransactions;

    private const string REGISTER_TASK_ENDPOINT = '/api/task';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $skippedTests = [
            'testItShouldReturnUnauthenticatedMessageWhenUserTryToCreateTaskButIsNotAuthenticated',
        ];

        if (in_array($this->name(), $skippedTests)) {
            return;
        }

        $this->user = User::factory()->createOne();

        auth('api')->attempt(['email' => $this->user->email, 'password' => 'P4sSW0rd@!)_']);
    }

    public function testItShouldReturnUnauthenticatedMessageWhenUserTryToCreateTaskButIsNotAuthenticated(): void
    {
        $this
            ->postJson(self::REGISTER_TASK_ENDPOINT, [])
            ->assertUnauthorized()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has('message')
                    ->whereType('message', 'string')
                    ->where('message', AuthMessage::UNAUTHENTICATED)
            );
    }

    public static function validateTaskTitleProvider(): array
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
                    ->where('error.title.0', TaskTitleMessage::taskTitleIsRequired()),
            ],
            'should return that the title field is longer than the allowed length when the value of the title field is more than the maximum' => [
                'title' => implode(',', fake()->paragraphs(TaskTitleEnum::MAX_LENGTH->value)),
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
                    ->where('error.title.0', TaskTitleMessage::taskTitleMaxLength()),
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
                    ->where('error.title.0', TaskTitleMessage::taskTitleMustBeString()),
            ],
        ];
    }

    #[DataProvider('validateTaskTitleProvider')]
    public function testValidateTaskTitle(string|int $title, Closure $response): void
    {
        $data = [
            'title' => $title,
            'description' => '',
        ];

        $this
            ->actingAs($this->user)
            ->postJson(self::REGISTER_TASK_ENDPOINT, $data)
            ->assertBadRequest()
            ->assertJson($response);
    }

    public static function validateTaskDescriptionProvider(): array
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
                    ->where('error.description.0', TaskDescriptionMessage::taskDescriptionIsRequired()),
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
                    ->where('error.description.0', TaskDescriptionMessage::taskDescriptionMustBeString()),
            ],
        ];
    }

    #[DataProvider('validateTaskDescriptionProvider')]
    public function testValidateTaskDescription(string|int $description, Closure $response): void
    {
        $data = [
            'title' => fake()->numerify('#############'),
            'description' => $description,
        ];

        $this
            ->actingAs($this->user)
            ->postJson(self::REGISTER_TASK_ENDPOINT, $data)
            ->assertBadRequest()
            ->assertJson($response);
    }

    public static function validateTaskStatusProvider(): array
    {
        return [
            'should return that the status field is required when the value of the status field is empty' => [
                'status' => '',
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.status',
                        'error.status.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.status' => 'array',
                        'error.status.0' => 'string',
                    ])
                    ->where('error.status.0', TaskStatusMessage::taskStatusIsRequired()),
            ],
            'should return that the status field is invalid when the value of the status field is not a valid status' => [
                'status' => fake()->name(),
                'response' => fn (AssertableJson $json): AssertableJson => $json
                    ->hasAll([
                        'error',
                        'error.status',
                        'error.status.0',
                    ])
                    ->whereAllType([
                        'error' => 'array',
                        'error.status' => 'array',
                        'error.status.0' => 'string',
                    ])
                    ->where('error.status.0', TaskStatusMessage::taskStatusIsInvalid()),
            ],
        ];
    }

    #[DataProvider('validateTaskStatusProvider')]
    public function testValidateTaskStatus(string $status, Closure $response): void
    {
        $data = [
            'title' => fake()->name(),
            'description' => fake()->sentence(),
            'status' => $status,
        ];

        $this
            ->actingAs($this->user)
            ->postJson(self::REGISTER_TASK_ENDPOINT, $data)
            ->assertBadRequest()
            ->assertJson($response);
    }

    public function testItShouldRegisterNewTaskAndReturnCreatedTaskData(): void
    {
        $data = [
            'title' => fake()->name(),
            'description' => fake()->sentence(),
            'status' => 'todo',
        ];

        $this
            ->actingAs($this->user)
            ->postJson(self::REGISTER_TASK_ENDPOINT, $data)
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->hasAll([
                    'data',
                    'data.id',
                    'data.title',
                    'data.description',
                    'data.status',
                    'data.user',
                    'data.user.id',
                    'data.user.name',
                    'data.created_at',
                    'data.updated_at',
                ])
                ->whereAllType([
                    'data' => 'array',
                    'data.id' => 'string',
                    'data.title' => 'string',
                    'data.description' => 'string',
                    'data.status' => 'string',
                    'data.user' => 'array',
                    'data.user.id' => 'string',
                    'data.user.name' => 'string',
                    'data.created_at' => 'string',
                    'data.updated_at' => 'string',
                ])
                ->whereAll([
                    'data.title' => $data['title'],
                    'data.description' => $data['description'],
                    'data.status' => $data['status'],
                    'data.user.id' => $this->user->id,
                    'data.user.name' => $this->user->name,
                ])
            );
    }
}
