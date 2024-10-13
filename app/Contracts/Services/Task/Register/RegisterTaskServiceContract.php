<?php

declare(strict_types=1);

namespace App\Contracts\Services\Task\Register;

use App\DTO\Task\Register\RegisterTaskDTO;
use App\Models\Task;

interface RegisterTaskServiceContract
{
    public function handle(RegisterTaskDTO $registerTaskDTO): Task;
}
