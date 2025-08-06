<?php

namespace Tests\Unit\Domain\Entity\User;

use App\Domain\Entity\User\Factory\UserFactory;
use App\Domain\Entity\User\Role\Exception\InvalidRoleTransitionException;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    #[Test]
    public function it_should_create_user_with_valid_data(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@DAsD)#@')
        );

        $this->assertEquals('John Doe', $user->getName()->getValue());
        $this->assertEquals('ilovelaravel@gmail.com', $user->getEmail()->getValue());
        $this->assertEquals('user', $user->getRole()->value);

        $this->assertNull($user->getId());
        $this->assertNull($user->getEmailConfirmedAt());

        $this->assertNotEquals('P4SsW0rd!@DAsD)#@', $user->getPassword()->getValue());

        $this->assertTrue($user->getPassword()->match(passwordAsPlainText: 'P4SsW0rd!@DAsD)#@'));
        $this->assertTrue($user->getRole()->isUser());

        $this->assertFalse($user->getRole()->isAdmin());
        $this->assertFalse($user->isEmailAlreadyConfirmed());
    }

    #[Test]
    public function it_should_allow_user_to_change_name(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@DAsD)#@')
        );

        $oldName = $user->getName();

        $user->changeNameTo(new Name('Jane Doe'));

        $this->assertNotEquals($oldName->getValue(), $user->getName()->getValue());
        $this->assertEquals('Jane Doe', $user->getName()->getValue());
    }

    #[Test]
    public function it_should_allow_user_to_change_email(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@DAsD)#@')
        );

        $oldEmail = $user->getEmail();

        $user->changeEmailTo(new Email('ilovephp@gmail.com'));

        $this->assertNotEquals($oldEmail->getValue(), $user->getEmail()->getValue());
        $this->assertEquals('ilovephp@gmail.com', $user->getEmail()->getValue());
    }

    #[Test]
    public function it_should_allow_user_to_change_password(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@DAsD)#@')
        );

        $oldPassword = $user->getPassword();

        $user->changePasswordTo(new Password('N3wP4SsW0rd!@DAsD)#@'));

        $this->assertNotEquals($oldPassword->getValue(), $user->getPassword()->getValue());

        $this->assertTrue($user->getPassword()->match(passwordAsPlainText: 'N3wP4SsW0rd!@DAsD)#@'));
        $this->assertFalse($user->getPassword()->match(passwordAsPlainText: 'P4SsW0rd!@DAsD)#@'));
    }

    #[Test]
    public function it_should_allow_user_to_become_admin(): void
    {
        $admin = new User(
            name: new Name('John Admin Data'),
            email: new Email('laraveladmin@gmail.com'),
            password: new Password('P4SSw0ord!@#dASDASDASW!@#ASD_'),
            role: Role::ADMIN,
        );

        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@DAsD)#@')
        );

        $user->becomeAdmin($admin);

        $this->assertEquals('admin', $user->getRole()->value);
        $this->assertTrue($user->getRole()->isAdmin());

        $this->assertNotEquals('user', $user->getRole()->value);
        $this->assertFalse($user->getRole()->isUser());
    }

    #[Test]
    public function it_should_throw_exception_when_non_admin_user_tries_to_become_admin(): void
    {
        $admin = UserFactory::create(
            name: new Name('John Admin Data'),
            email: new Email('laraveladmin@gmail.com'),
            password: new Password('P4SSw0ord!@#dASDASDASW!@#ASD_'),
        );

        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@DAsD)#@')
        );

        $this->expectException(InvalidRoleTransitionException::class);
        $this->expectExceptionMessage("Invalid role transition from 'user' to 'admin' on user 'ilovelaravel@gmail.com'");

        $user->becomeAdmin($admin);
    }

    #[Test]
    public function it_should_allow_admin_to_become_user(): void
    {
        $admin = new User(
            name: new Name('John Admin Data'),
            email: new Email('laraveladmin@gmail.com'),
            password: new Password('P4SSw0ord!@#dASDASDASW!@#ASD_'),
            role: Role::ADMIN,
        );

        $admin->becomeUser();

        $this->assertNotEquals('admin', $admin->getRole()->value);
        $this->assertFalse($admin->getRole()->isAdmin());

        $this->assertEquals('user', $admin->getRole()->value);
        $this->assertTrue($admin->getRole()->isUser());
    }

    #[Test]
    public function it_should_allow_user_to_confirm_email(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@DAsD)#@')
        );

        $this->assertNull($user->getEmailConfirmedAt());

        $this->assertFalse($user->isEmailAlreadyConfirmed());

        $user->markEmailAsConfirmed();

        $this->assertNotNull($user->getEmailConfirmedAt());
        $this->assertTrue($user->isEmailAlreadyConfirmed());
    }

    #[Test]
    public function it_should_transform_user_to_array(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@DAsD)#@')
        );

        $userArray = $user->toArray();

        $this->assertArrayHasKey('id', $userArray);
        $this->assertArrayHasKey('name', $userArray);
        $this->assertArrayHasKey('email', $userArray);
        $this->assertArrayHasKey('password', $userArray);
        $this->assertArrayHasKey('role', $userArray);
        $this->assertArrayHasKey('createdAt', $userArray);
        $this->assertArrayHasKey('updatedAt', $userArray);
        $this->assertArrayHasKey('emailConfirmedAt', $userArray);
    }
}
