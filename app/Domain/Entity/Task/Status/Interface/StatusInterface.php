<?php

namespace App\Domain\Entity\Task\Status\Interface;

use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\Task;

interface StatusInterface
{
    public function todo(Task $task): void;

    public function doing(Task $task): void;

    public function done(Task $task): void;

    public function cancelled(Task $task): void;

    public function getStatus(): Status;
}
