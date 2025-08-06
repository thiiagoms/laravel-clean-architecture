<?php

namespace Tests\Feature\Infrastructure\Persistence\Repository\User\Register;

use App\Domain\Entity\User\Factory\UserFactory;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Infrastructure\Persistence\Repository\User\Register\EloquentRegisterUserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EloquentRegisterUserRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_should_create_new_user_and_return_created_user_entity(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4sSw0RdStr0ng!@#@_'),
        );

        $result = (new EloquentRegisterUserRepository)->save($user);

        $this->assertEquals($user->getName()->getValue(), $result->getName()->getValue());
        $this->assertEquals($user->getEmail()->getValue(), $result->getEmail()->getValue());
        $this->assertEquals(Role::USER, $result->getRole());

        $this->assertTrue($result->getPassword()->match(passwordAsPlainText: 'P4sSw0RdStr0ng!@#@_'));

        $this->assertNotNull($result->getId());
    }
}
