<?php

namespace Tests\Feature\Task\Update;

use App\Enums\Task\TaskStatusEnum;
use App\Enums\Task\TaskTitleEnum;
use App\Infrastructure\Persistence\Model\Task;
use App\Infrastructure\Persistence\Model\User;
use App\Messages\Auth\AuthMessage;
use App\Messages\System\SystemMessage;
use App\Messages\Task\TaskDescriptionMessage;
use App\Messages\Task\TaskStatusMessage;
use App\Messages\Task\TaskTitleMessage;
use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UpdateTaskTest extends TestCase
{
    use DatabaseTransactions;

    private const string UPDATE_TASK_ENDPOINT = '/api/task';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $testWithoutAuth = 'testItShouldReturnUnauthenticatedMessageWhenUserTryToUpdateTaskButIsNotAuthenticated';

        if ($this->name() === $testWithoutAuth) {
            return;
        }

        $this->user = User::factory()->createOne();

        auth('api')->attempt(['email' => $this->user->email, 'password' => 'P4sSW0rd@!)_']);
    }

    public function testItShouldReturnUnauthenticatedMessageWhenUserTryToUpdateTaskButIsNotAuthenticated(): void
    {
        $this
            ->putJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.'fake-id')
            ->assertUnauthorized()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has('message')
                    ->whereType('message', 'string')
                    ->where('message', AuthMessage::UNAUTHENTICATED)
            );
    }

    public function testItShouldReturnResourceNotFoundMessageWhenUserTryToUpdateTaskThatDoesNotExists(): void
    {
        $this
            ->putJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.'fake-id')
            ->assertNotFound()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('message')
                ->whereType('message', 'string')
                ->where('message', SystemMessage::RESOURCE_NOT_FOUND)
            );
    }

    public function testItShouldReturnUnauthorizedMessageWhenUserTryToUpdateTaskThatDoesNotBelongsToUser(): void
    {
        $task = Task::factory()->createOne();

        $this
            ->patchJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id)
            ->assertForbidden()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('message')
                ->whereType('message', 'string')
                ->where('message', AuthMessage::UNAUTHORIZED)
            );
    }

    public static function validateTaskTitleProvider(): array
    {
        return [
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
        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $this
            ->actingAs($this->user)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id, ['title' => $title])
            ->assertBadRequest()
            ->assertJson($response);
    }

    public static function validateTaskDescriptionProvider(): array
    {
        return [
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
    public function testValidateTaskDescription(int $description, Closure $response): void
    {
        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $this
            ->actingAs($this->user)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id, ['description' => $description])
            ->assertBadRequest()
            ->assertJson($response);
    }

    public static function validateTaskStatusProvider(): array
    {
        return [
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
        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $this
            ->actingAs($this->user)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id, ['status' => $status])
            ->assertBadRequest()
            ->assertJson($response);
    }

    public function testItShouldUpdateOnlyTaskTitleWhenOnlyTitleIsProvidedAndReturnUpdatedTaskData(): void
    {
        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $title = fake()->name();

        $this
            ->actingAs($this->user)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id, ['title' => $title])
            ->assertOk()
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
                    'data.id' => $task->id,
                    'data.title' => $title,
                    'data.description' => $task->description,
                    'data.status' => $task->status->value,
                    'data.user.id' => $this->user->id,
                    'data.user.name' => $this->user->name,
                ])
            );
    }

    public function testItShouldUpdateOnlyTaskDescriptionWhenOnlyDescriptionIsProvidedAndReturnUpdatedTaskData(): void
    {
        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $description = fake()->name();

        $this
            ->actingAs($this->user)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id, ['description' => $description])
            ->assertOk()
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
                    'data.id' => $task->id,
                    'data.title' => $task->title,
                    'data.description' => $description,
                    'data.status' => $task->status->value,
                    'data.user.id' => $this->user->id,
                    'data.user.name' => $this->user->name,
                ])
            );
    }

    public function testItShouldUpdateOnlyTaskStatusWhenOnlyStatusIsProvidedAndReturnUpdatedTaskData(): void
    {
        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $status = TaskStatusEnum::DOING->value;

        $this
            ->actingAs($this->user)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id, ['status' => $status])
            ->assertOk()
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
                    'data.id' => $task->id,
                    'data.title' => $task->title,
                    'data.description' => $task->description,
                    'data.status' => $status,
                    'data.user.id' => $this->user->id,
                    'data.user.name' => $this->user->name,
                ])
            );
    }

    public function testItShouldUpdateEntirelyTaskWhenEverythingIsProvidedAndReturnUpdatedTaskData(): void
    {
        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $title = fake()->name();
        $description = fake()->name();
        $status = TaskStatusEnum::DOING->value;

        $this
            ->actingAs($this->user)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id, [
                'title' => $title,
                'description' => $description,
                'status' => $status,
            ])
            ->assertOk()
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
                    'data.id' => $task->id,
                    'data.title' => $title,
                    'data.description' => $description,
                    'data.status' => $status,
                    'data.user.id' => $this->user->id,
                    'data.user.name' => $this->user->name,
                ])
            );
    }
}
