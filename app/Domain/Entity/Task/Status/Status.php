<?php

namespace App\Domain\Entity\Task\Status;

enum Status: string
{
    case TODO = 'todo';
    case DOING = 'doing';
    case DONE = 'done';
    case CANCELLED = 'cancelled';

    public function isToDo(): bool
    {
        return $this === self::TODO;
    }

    public function isDoing(): bool
    {
        return $this === self::DOING;
    }

    public function isDone(): bool
    {
        return $this === self::DONE;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }
}
