<?php

declare(strict_types=1);

namespace App\Enums\Task;

enum TaskStatusEnum: string
{
    case TODO = 'todo';
    case DOING = 'doing';
    case DONE = 'done';
    case CANCELLED = 'cancelled';
}
