<?php

namespace App\Domain\Entity\Task\Status\implementation;

use App\Domain\Entity\Task\Status\Exception\InvalidTaskStatusTransitionException;
use App\Domain\Entity\Task\Status\Interface\StatusInterface;
use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\Task;

class Todo implements StatusInterface
{
    public function todo(Task $task): void
    {
        throw InvalidTaskStatusTransitionException::create(
            from: Status::TODO,
            to: Status::TODO,
            owner: $task->getOwner()
        );
    }

    public function doing(Task $task): void
    {
        $task->setStatus(new Doing);
    }

    public function done(Task $task): void
    {
        throw InvalidTaskStatusTransitionException::create(
            from: Status::TODO,
            to: Status::DONE,
            owner: $task->getOwner()
        );
    }

    public function cancelled(Task $task): void
    {
        $task->setStatus(new Cancelled);
    }

    public function getStatus(): Status
    {
        return Status::TODO;
    }
}
