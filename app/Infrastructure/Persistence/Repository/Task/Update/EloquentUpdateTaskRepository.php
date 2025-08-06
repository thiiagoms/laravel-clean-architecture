<?php

namespace App\Infrastructure\Persistence\Repository\Task\Update;

use App\Domain\Entity\Task\Task;
use App\Domain\Repository\Task\Update\UpdateTaskRepositoryInterface;
use App\Infrastructure\Persistence\Repository\Task\BaseTaskRepository;
use Illuminate\Support\Facades\DB;

final class EloquentUpdateTaskRepository extends BaseTaskRepository implements UpdateTaskRepositoryInterface
{
    public function update(Task $task): Task
    {
        $updateWasSuccess = (bool) DB::table('tasks')
            ->where('id', $task->getId()->getValue())
            ->update([
                'title' => $task->getTitle()->getValue(),
                'description' => $task->getDescription()->getValue(),
                'status' => $task->getStatus()->value,
                'updated_at' => now(),
            ]);

        if (! $updateWasSuccess) {
            throw new \RuntimeException('Failed to update task.');
        }

        return $task;
    }
}
