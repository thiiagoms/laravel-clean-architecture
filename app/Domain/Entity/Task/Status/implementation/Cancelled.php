<?php

namespace App\Domain\Entity\Task\Status\implementation;

use App\Domain\Entity\Task\Status\Exception\InvalidTaskStatusTransitionException;
use App\Domain\Entity\Task\Status\Interface\StatusInterface;
use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\Task;

class Cancelled implements StatusInterface
{
    public function todo(Task $task): void
    {
        throw InvalidTaskStatusTransitionException::create(
            from: Status::CANCELLED,
            to: Status::TODO,
            owner: $task->getOwner()
        );
    }

    public function doing(Task $task): void
    {
        throw InvalidTaskStatusTransitionException::create(
            from: Status::CANCELLED,
            to: Status::DOING,
            owner: $task->getOwner()
        );
    }

    public function done(Task $task): void
    {
        throw InvalidTaskStatusTransitionException::create(
            from: Status::CANCELLED,
            to: Status::DONE,
            owner: $task->getOwner()
        );
    }

    public function cancelled(Task $task): void
    {
        throw InvalidTaskStatusTransitionException::create(
            from: Status::CANCELLED,
            to: Status::CANCELLED,
            owner: $task->getOwner()
        );
    }

    public function getStatus(): Status
    {
        return Status::CANCELLED;
    }
}
