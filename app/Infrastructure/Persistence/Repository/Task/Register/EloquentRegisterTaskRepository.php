<?php

namespace App\Infrastructure\Persistence\Repository\Task\Register;

use App\Domain\Entity\Task\Task;
use App\Domain\Repository\Task\Register\RegisterTaskRepositoryInterface;
use App\Infrastructure\Persistence\Mapper\Task\TaskMapper;
use App\Infrastructure\Persistence\Mapper\User\UserMapper;
use App\Infrastructure\Persistence\Repository\Task\BaseTaskRepository;

final class EloquentRegisterTaskRepository extends BaseTaskRepository implements RegisterTaskRepositoryInterface
{
    public function save(Task $task): Task
    {
        $data = TaskMapper::toPersistence($task);

        $task = $this->model->create($data);

        return TaskMapper::toDomain($task, UserMapper::toDomain($task->user));
    }
}
