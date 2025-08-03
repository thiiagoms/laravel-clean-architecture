<?php

namespace Feature\Infrastructure\Persistence\Repository\Task\Find;

use App\Domain\Common\ValueObject\Id;
use App\Infrastructure\Persistence\Model\Task as LaravelTaskModel;
use App\Infrastructure\Persistence\Repository\Task\Find\EloquentFindTaskByIdRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EloquentFindTaskByIdRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private Id $id;

    private EloquentFindTaskByIdRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->id = new Id(fake()->uuid());

        $this->repository = new EloquentFindTaskByIdRepository;
    }

    #[Test]
    public function itShouldReturnTaskWhenTaskWithIdExistsInDatabase(): void
    {
        LaravelTaskModel::factory()->create(['id' => $this->id->getValue()]);

        $result = $this->repository->find($this->id);

        $this->assertEquals($result->getId()->getValue(), $this->id->getValue());
    }

    #[Test]
    public function itShouldReturnNullWhenTaskWithIdDoesNotExistInDatabase(): void
    {
        $result = $this->repository->find($this->id);

        $this->assertNull($result);
    }
}
