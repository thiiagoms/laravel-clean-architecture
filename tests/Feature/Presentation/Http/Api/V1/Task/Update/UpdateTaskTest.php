<?php

namespace Feature\Presentation\Http\Api\V1\Task\Update;

use App\Domain\Entity\Task\Status\Status;
use App\Infrastructure\Persistence\Model\Task as LaravelTaskModel;
use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateTaskTest extends TestCase
{
    use DatabaseTransactions;

    private const string UPDATE_TASK_ENDPOINT = '/api/v1/task/';

    #[Test]
    public function it_should_not_allow_to_update_task_when_user_is_not_authenticated(): void
    {
        $this
            ->patchJson(self::UPDATE_TASK_ENDPOINT.fake()->uuid())
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'Unauthenticated.')
            );
    }

    #[Test]
    public function it_should_not_allow_to_update_task_when_user_is_authenticated_but_task_does_not_exists(): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.fake()->uuid())
            ->assertNotFound()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'resource not found')
            );
    }

    #[Test]
    public function it_should_not_allow_to_update_task_when_user_is_authenticated_and_task_exists_but_does_not_belongs_to_user(): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne();

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.$task->id)
            ->assertForbidden()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'oops! It looks like you don’t have access to this resource.')
            );
    }

    public static function invalidTitleCases(): array
    {
        return [
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
    public function it_should_validate_title(string|int $title, \Closure $response): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne(['user_id' => $owner->id]);

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.$task->id, ['title' => $title])
            ->assertBadRequest()
            ->assertJson($response);
    }

    public static function invalidDescriptionCases(): array
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
                    ->where('error.description.0', 'The description field must be a string.'),
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidDescriptionCases')]
    public function it_should_validate_description(string|int $description, \Closure $response): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne(['user_id' => $owner->id]);

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.$task->id, ['description' => $description])
            ->assertBadRequest()
            ->assertJson($response);
    }

    public static function notAllowUpdateTaskWithStatusCases(): array
    {
        return [
            'should throw exception when status is done' => [Status::DONE],
            'should throw exception when status is cancelled' => [Status::CANCELLED],
        ];
    }

    #[Test]
    #[DataProvider('notAllowUpdateTaskWithStatusCases')]
    public function it_should_not_allow_to_update_task_with_not_allowed_status(Status $status): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne(['user_id' => $owner->id, 'status' => $status->value]);

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.$task->id, ['title' => 'new title'])
            ->assertBadRequest()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'Task cannot be updated')
            );
    }

    public static function invalidStatusCases(): array
    {
        return [
            'should return that the status field must be a string when the status field is not a valid string' => [
                'status' => 123,
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
                    ->where('error.status.0', 'The status field must be a string.'),
            ],
            'should return that the status field must be one of the allowed values when the status field is not a valid status' => [
                'status' => 'invalid_status',
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
                    ->where('error.status.0', 'The selected status is invalid.'),
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidStatusCases')]
    public function it_should_validate_status(string|int $status, \Closure $response): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne(['user_id' => $owner->id]);

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.$task->id, ['status' => $status])
            ->assertBadRequest()
            ->assertJson($response);
    }

    #[Test]
    public function it_should_update_only_task_title(): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne(['user_id' => $owner->id]);

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.$task->id, ['title' => 'new title'])
            ->assertOk()
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
                    'data.status' => 'string',
                    'data.description' => 'string',
                    'data.created_at' => 'string',
                    'data.updated_at' => 'string',
                ])
                ->whereAll([
                    'data.id' => $task->id,
                    'data.title' => 'new title',
                    'data.status' => $task->status,
                    'data.description' => $task->description,
                ])
            );

    }

    #[Test]
    public function it_should_update_only_task_description(): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne(['user_id' => $owner->id]);

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.$task->id, ['description' => 'new description'])
            ->assertOk()
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
                    'data.status' => 'string',
                    'data.description' => 'string',
                    'data.created_at' => 'string',
                    'data.updated_at' => 'string',
                ])
                ->whereAll([
                    'data.id' => $task->id,
                    'data.title' => $task->title,
                    'data.status' => $task->status,
                    'data.description' => 'new description',
                ])
            );
    }

    #[Test]
    public function it_should_update_only_task_status(): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne(['user_id' => $owner->id]);

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.$task->id, ['status' => 'done'])
            ->assertOk()
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
                    'data.status' => 'string',
                    'data.description' => 'string',
                    'data.created_at' => 'string',
                    'data.updated_at' => 'string',
                ])
                ->whereAll([
                    'data.id' => $task->id,
                    'data.title' => $task->title,
                    'data.status' => 'done',
                    'data.description' => $task->description,
                ])
            );
    }

    #[Test]
    public function it_should_update_task_with_all_fields(): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne(['user_id' => $owner->id]);

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->patchJson(self::UPDATE_TASK_ENDPOINT.$task->id, [
                'title' => 'new title',
                'description' => 'new description',
                'status' => 'done',
            ])
            ->assertOk()
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
                    'data.status' => 'string',
                    'data.description' => 'string',
                    'data.created_at' => 'string',
                    'data.updated_at' => 'string',
                ])
                ->whereAll([
                    'data.id' => $task->id,
                    'data.title' => 'new title',
                    'data.status' => 'done',
                    'data.description' => 'new description',
                ])
            );
    }
}
