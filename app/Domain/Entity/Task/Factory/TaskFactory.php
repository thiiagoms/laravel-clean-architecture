<?php

namespace App\Domain\Entity\Task\Factory;

use App\Domain\Entity\Task\Interface\TaskOwnerInterface;
use App\Domain\Entity\Task\Status\implementation\Todo;
use App\Domain\Entity\Task\Task;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;

abstract class TaskFactory
{
    public static function create(Title $title, Description $description, TaskOwnerInterface $owner): Task
    {
        return new Task(
            title: $title,
            description: $description,
            owner: $owner,
            status: new Todo
        );
    }
}
