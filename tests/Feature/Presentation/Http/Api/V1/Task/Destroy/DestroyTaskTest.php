<?php

namespace Feature\Presentation\Http\Api\V1\Task\Destroy;

use App\Infrastructure\Persistence\Model\Task as LaravelTaskModel;
use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyTaskTest extends TestCase
{
    use DatabaseTransactions;

    private const string DESTROY_TASK_ENDPOINT = '/api/v1/task/';

    #[Test]
    public function it_should_not_allow_to_destroy_task_when_user_is_not_authenticated(): void
    {
        $this
            ->deleteJson(self::DESTROY_TASK_ENDPOINT.fake()->uuid())
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'Unauthenticated.')
            );
    }

    #[Test]
    public function it_should_not_allow_to_destroy_task_when_user_is_authenticated_but_task_does_not_exists(): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->deleteJson(self::DESTROY_TASK_ENDPOINT.fake()->uuid())
            ->assertNotFound()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'resource not found')
            );
    }

    #[Test]
    public function it_should_not_allow_to_destroy_task_when_user_is_authenticated_and_task_exists_but_does_not_belongs_to_user(): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne();

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->deleteJson(self::DESTROY_TASK_ENDPOINT.$task->id)
            ->assertForbidden()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('error')
                ->whereType('error', 'string')
                ->where('error', 'oops! It looks like you don’t have access to this resource.')
            );
    }

    #[Test]
    public function it_should_destroy_task_tha_belongs_t_oo_authenticated_user(): void
    {
        $owner = LaravelUserModel::factory()->createOne();

        $task = LaravelTaskModel::factory()->createOne(['user_id' => $owner->id]);

        auth('api')->attempt(['email' => $owner->email, 'password' => 'P4sSW0rd@!)_']);

        $this
            ->actingAs($owner)
            ->deleteJson(self::DESTROY_TASK_ENDPOINT.$task->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
