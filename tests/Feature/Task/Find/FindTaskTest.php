<?php

namespace Tests\Feature\Task\Find;

use App\Messages\Auth\AuthMessage;
use App\Messages\System\SystemMessage;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FindTaskTest extends TestCase
{
    use DatabaseTransactions;

    private const string FIND_TASK_ENDPOINT = '/api/task';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $testWithoutAuth = 'testItShouldReturnUnauthenticatedMessageWhenUserTryToGetTaskButIsNotAuthenticated';

        if ($this->name() === $testWithoutAuth) {
            return;
        }

        $this->user = User::factory()->createOne();

        auth('api')->attempt(['email' => $this->user->email, 'password' => 'P4sSW0rd@!)_']);
    }

    public function testItShouldReturnUnauthenticatedMessageWhenUserTryToGetTaskButIsNotAuthenticated(): void
    {
        $this
            ->getJson(self::FIND_TASK_ENDPOINT)
            ->assertUnauthorized()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has('message')
                    ->whereType('message', 'string')
                    ->where('message', AuthMessage::UNAUTHENTICATED)
            );
    }

    public function testItShouldReturnResourceNotFoundMessageWhenUserTryToGetTaskThatDoesNotExists(): void
    {
        $this
            ->getJson(self::FIND_TASK_ENDPOINT.DIRECTORY_SEPARATOR.fake()->uuid())
            ->assertNotFound()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has('message')
                    ->whereType('message', 'string')
                    ->where('message', SystemMessage::RESOURCE_NOT_FOUND)
            );
    }

    public function testItShouldReturnUnauthorizedMessageWhenUserTryToGetTaskThatDoesNotBelongsToUser(): void
    {
        $task = Task::factory()->createOne();

        $this
            ->getJson(self::FIND_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id)
            ->assertForbidden()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has('message')
                    ->whereType('message', 'string')
                    ->where('message', AuthMessage::UNAUTHORIZED)
            );
    }

    public function testItShouldReturnUserTaskWhenUserTryToGetTaskThatBelongsToUser(): void
    {
        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $this
            ->getJson(self::FIND_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
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
                        'data.title' => $task->title,
                        'data.description' => $task->description,
                        'data.status' => $task->status->value,
                        'data.user.id' => $this->user->id,
                        'data.user.name' => $this->user->name,
                    ])
            );
    }
}
