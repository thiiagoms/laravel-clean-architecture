<?php

declare(strict_types=1);

namespace App\Repositories\Task;

use App\Contracts\Repositories\Task\TaskRepositoryContract as TaskTaskRepositoryContract;
use App\Models\Task;
use App\Repositories\BaseRepository;

class TaskRepository extends BaseRepository implements TaskTaskRepositoryContract
{
    /** @var Task */
    protected $model = Task::class;
}
