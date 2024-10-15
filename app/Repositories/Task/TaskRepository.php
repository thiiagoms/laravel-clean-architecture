<?php

declare(strict_types=1);

namespace App\Repositories\Task;

use App\Contracts\Repositories\Task\TaskRepositoryContract as TaskTaskRepositoryContract;
use App\Models\Task;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository extends BaseRepository implements TaskTaskRepositoryContract
{
    /** @var Task */
    protected $model = Task::class;

    public function findUserTasks(string $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
