<?php

namespace Tests\Unit\Domain\Entity\Task;

use App\Domain\Entity\Task\Factory\TaskFactory;
use App\Domain\Entity\Task\Status\Exception\InvalidTaskStatusTransitionException;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Domain\Entity\User\Factory\UserFactory;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    #[Test]
    public function it_should_create_task_with_valid_data(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $this->assertEquals('Task title example', $task->getTitle()->getValue());
        $this->assertEquals('Task description example', $task->getDescription()->getValue());
        $this->assertEquals('todo', $task->getStatus()->value);
        $this->assertTrue($task->getStatus()->isToDo());

        $this->assertEquals('John Doe', $task->getOwner()->getName()->getValue());
        $this->assertEquals('ilovelaravel@gmail.com', $task->getOwner()->getEmail()->getValue());

        $this->assertNull($task->getId());
    }

    #[Test]
    public function it_should_allow_task_with_todo_status_to_go_to_doing(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->doing();

        $this->assertEquals('doing', $task->getStatus()->value);
        $this->assertTrue($task->getStatus()->isDoing());

        $this->assertNotEquals('todo', $task->getStatus()->value);
        $this->assertFalse($task->getStatus()->isToDo());
    }

    #[Test]
    public function it_should_allow_task_with_todo_status_to_go_to_cancelled(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->cancelled();

        $this->assertEquals('cancelled', $task->getStatus()->value);
        $this->assertTrue($task->getStatus()->isCancelled());

        $this->assertNotEquals('todo', $task->getStatus()->value);
        $this->assertFalse($task->getStatus()->isToDo());
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_todo_status_goes_to_todo_again(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'todo' to 'todo' for task owned by 'ilovelaravel@gmail.com'.");

        $task->todo();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_todo_status_goes_to_done(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'todo' to 'done' for task owned by 'ilovelaravel@gmail.com'.");

        $task->done();
    }

    #[Test]
    public function it_should_allow_task_with_doing_status_to_go_to_done(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->doing();

        $task->done();

        $this->assertEquals('done', $task->getStatus()->value);
        $this->assertTrue($task->getStatus()->isDone());

        $this->assertNotEquals('doing', $task->getStatus()->value);
        $this->assertFalse($task->getStatus()->isDoing());
    }

    #[Test]
    public function it_should_allow_task_with_doing_status_to_go_to_cancelled(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->doing();

        $task->done();

        $this->assertEquals('done', $task->getStatus()->value);
        $this->assertTrue($task->getStatus()->isDone());

        $this->assertNotEquals('doing', $task->getStatus()->value);
        $this->assertFalse($task->getStatus()->isDoing());
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_doing_status_goes_to_todo(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->doing();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'doing' to 'todo' for task owned by 'ilovelaravel@gmail.com'.");

        $task->todo();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_doing_status_goes_to_doing_again(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->doing();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'doing' to 'doing' for task owned by 'ilovelaravel@gmail.com'.");

        $task->doing();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_done_status_goes_to_todo(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->doing();

        $task->done();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'done' to 'todo' for task owned by 'ilovelaravel@gmail.com'.");

        $task->todo();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_done_status_goes_to_doing(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->doing();

        $task->done();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'done' to 'doing' for task owned by 'ilovelaravel@gmail.com'.");

        $task->doing();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_done_status_goes_to_done_again(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->doing();

        $task->done();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'done' to 'done' for task owned by 'ilovelaravel@gmail.com'.");

        $task->done();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_done_status_goes_to_cancelled(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->doing();

        $task->done();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'done' to 'cancelled' for task owned by 'ilovelaravel@gmail.com'.");

        $task->cancelled();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_cancelled_status_goes_to_todo(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->cancelled();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'cancelled' to 'todo' for task owned by 'ilovelaravel@gmail.com'.");

        $task->todo();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_cancelled_status_goes_to_doing(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->cancelled();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'cancelled' to 'doing' for task owned by 'ilovelaravel@gmail.com'.");

        $task->doing();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_cancelled_status_goes_to_done_again(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->cancelled();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'cancelled' to 'done' for task owned by 'ilovelaravel@gmail.com'.");

        $task->done();
    }

    #[Test]
    public function it_should_throw_exception_when_task_with_cancelled_status_goes_to_cancelled_again(): void
    {
        $user = UserFactory::create(
            name: new Name('John Doe'),
            email: new Email('ilovelaravel@gmail.com'),
            password: new Password('P4SsW0rd!@#DASD_')
        );

        $task = TaskFactory::create(
            title: new Title('Task title example'),
            description: new Description('Task description example'),
            owner: $user,
        );

        $task->cancelled();

        $this->expectException(InvalidTaskStatusTransitionException::class);
        $this->expectExceptionMessage("Invalid status transition from 'cancelled' to 'cancelled' for task owned by 'ilovelaravel@gmail.com'.");

        $task->cancelled();
    }
}
