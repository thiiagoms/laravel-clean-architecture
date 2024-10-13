<?php

declare(strict_types=1);

namespace App\Messages\Task;

use App\Enums\Task\TaskTitleEnum;
use App\Messages\BaseMessage;

abstract class TaskTitleMessage extends BaseMessage
{
    public static function taskTitleIsRequired(): string
    {
        return sprintf(self::FIELD_REQUIRED, 'title');
    }

    public static function taskTitleMaxLength(): string
    {
        return sprintf(self::FIELD_MAX_LENGTH, 'title', TaskTitleEnum::MAX_LENGTH->value);
    }

    public static function taskTitleMustBeString(): string
    {
        return sprintf(self::FIELD_TYPE, 'title', 'string');
    }
}
