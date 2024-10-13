<?php

declare(strict_types=1);

namespace App\Messages\Task;

use App\Messages\BaseMessage;

abstract class TaskStatusMessage extends BaseMessage
{
    public static function taskStatusIsRequired(): string
    {
        return sprintf(self::FIELD_REQUIRED, 'status');
    }

    public static function taskStatusIsInvalid(): string
    {
        return sprintf(self::FIELD_TYPE, 'status', 'status: todo, doing, done ou cancelled');
    }
}
