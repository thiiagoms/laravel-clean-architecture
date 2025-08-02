<?php

namespace Tests\Feature\Infrastructure\Persistence\Repository\User\Find;

use App\Domain\Common\ValueObject\Id;
use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use App\Infrastructure\Persistence\Repository\User\Find\EloquentFindUserByIdRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EloquentFindUserByIdRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private Id $id;

    private EloquentFindUserByIdRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->id = new Id('110ee162-e782-4334-9429-68306a5a6707');

        $this->repository = new EloquentFindUserByIdRepository;
    }

    #[Test]
    public function itShouldReturnUserWhenUserWithIdExistsInDatabase(): void
    {
        LaravelUserModel::factory()->createOne(['id' => $this->id->getValue()]);

        $result = $this->repository->find($this->id);

        $this->assertEquals($result->getId()->getValue(), $this->id->getValue());
    }

    #[Test]
    public function itShouldReturnNullWhenUserWithIdDoesNotExistInDatabase(): void
    {
        $result = $this->repository->find($this->id);

        $this->assertNull($result);
    }
}
