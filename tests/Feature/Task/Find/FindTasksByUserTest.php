<?php

namespace Tests\Feature\Task\Find;

use App\Messages\Auth\AuthMessage;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FindTasksByUserTest extends TestCase
{
    use DatabaseTransactions;

    private const string FIND_TASKS_BY_USER_ENDPOINT = '/api/task';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $testWithoutAuth = 'testItShouldReturnUnauthenticatedMessageWhenUserTryToGetTasksButIsNotAuthenticated';

        if ($this->name() === $testWithoutAuth) {
            return;
        }

        $this->user = User::factory()->createOne();

        auth('api')->attempt(['email' => $this->user->email, 'password' => 'P4sSW0rd@!)_']);
    }

    public function testItShouldReturnUnauthenticatedMessageWhenUserTryToGetTasksButIsNotAuthenticated(): void
    {
        $this
            ->getJson(self::FIND_TASKS_BY_USER_ENDPOINT)
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('message')
                ->whereType('message', 'string')
                ->where('message', AuthMessage::UNAUTHENTICATED)
            );
    }

    public function testItShouldReturnEmptyArrayWhenUserTryToGetTaskThatDoesNotExists(): void
    {
        $this
            ->getJson(self::FIND_TASKS_BY_USER_ENDPOINT)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('data')
                ->whereType('data', 'array')
                ->where('data', [])
            );
    }

    public function testItShouldReturnUserTasksWhenUserTryToGetTasks(): void
    {
        Task::factory(10)->create(['user_id' => $this->user->id]);

        $this
            ->getJson(self::FIND_TASKS_BY_USER_ENDPOINT)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('data', 10));
    }
}
