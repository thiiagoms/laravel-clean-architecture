<?php

namespace App\Domain\Entity\Task\Status\implementation;

use App\Domain\Entity\Task\Status\Exception\InvalidTaskStatusTransitionException;
use App\Domain\Entity\Task\Status\Interface\StatusInterface;
use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\Task;

class Doing implements StatusInterface
{
    public function todo(Task $task): void
    {
        throw InvalidTaskStatusTransitionException::create(
            from: Status::DOING,
            to: Status::TODO,
            owner: $task->getOwner()
        );
    }

    public function doing(Task $task): void
    {
        throw InvalidTaskStatusTransitionException::create(
            from: Status::DOING,
            to: Status::DOING,
            owner: $task->getOwner()
        );
    }

    public function done(Task $task): void
    {
        $task->setStatus(new Done);
    }

    public function cancelled(Task $task): void
    {
        $task->setStatus(new Cancelled);
    }

    public function getStatus(): Status
    {
        return Status::DOING;
    }
}
