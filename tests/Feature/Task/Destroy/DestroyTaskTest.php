<?php

namespace Tests\Feature\Task\Destroy;

use App\Enums\User\UserRoleEnum;
use App\Messages\Auth\AuthMessage;
use App\Messages\System\SystemMessage;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DestroyTaskTest extends TestCase
{
    use DatabaseTransactions;

    private const string DELETE_TASK_ENDPOINT = '/api/task';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $testWithoutAuth = 'testItShouldReturnUnauthenticatedMessageWhenUserTryToDestroyTaskButIsNotAuthenticated';

        if ($this->name() === $testWithoutAuth) {
            return;
        }

        $this->user = User::factory()->createOne();

        auth('api')->attempt(['email' => $this->user->email, 'password' => 'P4sSW0rd@!)_']);
    }

    public function testItShouldReturnUnauthenticatedMessageWhenUserTryToDestroyTaskButIsNotAuthenticated(): void
    {
        $this
            ->deleteJson(self::DELETE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.'fake-id')
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('message')
                ->whereType('message', 'string')
                ->where('message', AuthMessage::UNAUTHENTICATED)
            );
    }

    public function testItShouldReturnResourceNotFoundMessageWhenUserTryToDestroyTaskThatDoesNotExists(): void
    {
        $this
            ->deleteJson(self::DELETE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.'fake-id')
            ->assertNotFound()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has('message')
                    ->whereType('message', 'string')
                    ->where('message', SystemMessage::RESOURCE_NOT_FOUND)
            );
    }

    public function testItShouldReturnNoContentResponseWhenUserDestroyTaskThatBelongsToUser(): void
    {
        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $this
            ->deleteJson(self::DELETE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id)
            ->assertNoContent();
    }

    public function testShouldReturnNoContentResponseWhenUserHasAdminRoleAndDestroyAnotherUserTask(): void
    {
        $user = User::factory()->createOne(['role' => UserRoleEnum::ADMIN]);

        $task = Task::factory()->createOne(['user_id' => $this->user->id]);

        $this
            ->deleteJson(self::DELETE_TASK_ENDPOINT.DIRECTORY_SEPARATOR.$task->id)
            ->assertNoContent();
    }
}
