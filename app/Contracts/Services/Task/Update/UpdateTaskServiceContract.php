<?php

declare(strict_types=1);

namespace App\Contracts\Services\Task\Update;

use App\DTO\Task\Update\UpdateTaskDTO;
use App\Infrastructure\Persistence\Model\Task;

interface UpdateTaskServiceContract
{
    public function handle(UpdateTaskDTO $updateTaskDTO): Task;
}
