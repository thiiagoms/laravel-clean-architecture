<?php

namespace Feature\Infrastructure\Persistence\Repository\Task\Destroy;

use App\Domain\Common\ValueObject\Id;
use App\Infrastructure\Persistence\Model\Task as LaravelTaskModel;
use App\Infrastructure\Persistence\Repository\Task\Destroy\EloquentDestroyTaskByIdRepository;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EloquentDestroyTaskByIdRepositoryTest extends TestCase
{
    private EloquentDestroyTaskByIdRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentDestroyTaskByIdRepository;
    }

    #[Test]
    public function it_should_return_true_when_task_is_destroyed(): void
    {
        $id = new Id(fake()->uuid());

        LaravelTaskModel::factory()->create(['id' => $id->getValue()]);

        $result = $this->repository->destroy($id);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_should_return_false_when_task_is_not_destroyed(): void
    {
        $id = new Id(fake()->uuid());

        $result = $this->repository->destroy($id);

        $this->assertFalse($result);
    }
}
