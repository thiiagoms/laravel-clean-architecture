<?php

namespace App\Application\UseCases\Task\Register\Service;

use App\Domain\Entity\Task\Task;
use App\Domain\Repository\Task\Register\RegisterTaskRepositoryInterface;

class RegisterTaskService
{
    public function __construct(private readonly RegisterTaskRepositoryInterface $repository) {}

    public function handle(Task $task): Task
    {
        return $this->repository->save($task);
    }
}
