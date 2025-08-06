<?php

namespace App\Infrastructure\Persistence\Repository\Task\Find;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Task;
use App\Domain\Repository\Task\Find\FindTaskByIdRepositoryInterface;
use App\Infrastructure\Persistence\Mapper\Task\TaskMapper;
use App\Infrastructure\Persistence\Mapper\User\UserMapper;
use App\Infrastructure\Persistence\Repository\Task\BaseTaskRepository;

final class EloquentFindTaskByIdRepository extends BaseTaskRepository implements FindTaskByIdRepositoryInterface
{
    public function find(Id $id): ?Task
    {
        $task = $this->model->find($id->getValue());

        return empty($task) ? null : TaskMapper::toDomain(model: $task, owner: UserMapper::toDomain($task->user));
    }
}
