<?php

declare(strict_types=1);

namespace App\Messages\Task;

use App\Messages\BaseMessage;

abstract class TaskDescriptionMessage extends BaseMessage
{
    public static function taskDescriptionIsRequired(): string
    {
        return sprintf(self::FIELD_REQUIRED, 'description');
    }

    public static function taskDescriptionMustBeString(): string
    {
        return sprintf(self::FIELD_TYPE, 'description', 'string');
    }
}
