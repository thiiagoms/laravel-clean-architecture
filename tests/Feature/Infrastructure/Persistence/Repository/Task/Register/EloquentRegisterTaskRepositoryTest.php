<?php

namespace Tests\Feature\Infrastructure\Persistence\Repository\Task\Register;

use App\Domain\Entity\Task\Factory\TaskFactory;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Infrastructure\Persistence\Mapper\User\UserMapper;
use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use App\Infrastructure\Persistence\Repository\Task\Register\EloquentRegisterTaskRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EloquentRegisterTaskRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_should_create_new_task_and_return_created_task_entity(): void
    {
        $laravelUserModel = LaravelUserModel::factory()->createOne();

        $user = UserMapper::toDomain($laravelUserModel);

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user
        );

        $repository = new EloquentRegisterTaskRepository;

        $result = $repository->save($task);

        $this->assertEquals('Task title example', $result->getTitle()->getValue());
        $this->assertEquals('Task description example', $result->getDescription()->getValue());

        $this->assertNotNull($result->getId());
        $this->assertTrue(uuid_is_valid($result->getId()->getValue()));
    }
}
