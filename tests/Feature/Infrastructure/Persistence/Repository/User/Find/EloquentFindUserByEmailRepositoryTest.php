<?php

namespace Tests\Feature\Infrastructure\Persistence\Repository\User\Find;

use App\Domain\Entity\User\ValueObject\Email;
use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use App\Infrastructure\Persistence\Repository\User\Find\EloquentFindUserByEmailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EloquentFindUserByEmailRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private Email $email;

    private EloquentFindUserByEmailRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->email = new Email('ilovelaravel@gmail.com');

        $this->repository = new EloquentFindUserByEmailRepository;
    }

    #[Test]
    public function itShouldReturnUserWhenUserWithEmailExistsInDatabase(): void
    {
        LaravelUserModel::factory()->createOne(['email' => $this->email->getValue()]);

        $result = $this->repository->find($this->email);

        $this->assertEquals($result->getEmail()->getValue(), $this->email->getValue());
    }

    #[Test]
    public function itShouldReturnNullWhenUserWithEmailDoesNotExistInDatabase(): void
    {
        $result = $this->repository->find($this->email);

        $this->assertNull($result);
    }
}
