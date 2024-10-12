<?php

declare(strict_types=1);

namespace App\Messages\User;

use App\Enums\User\UserNameEnum;
use App\Messages\BaseMessage;

abstract class UserNameMessage extends BaseMessage
{
    public static function nameIsRequired(): string
    {
        return sprintf(self::FIELD_REQUIRED, 'name');
    }

    public static function nameMinLength(): string
    {
        return sprintf(self::FIELD_MIN_LENGTH, 'name', UserNameEnum::MIN_LENGTH->value);
    }

    public static function nameMaxLength(): string
    {
        return sprintf(self::FIELD_MAX_LENGTH, 'name', UserNameEnum::MAX_LENGTH->value);
    }

    public static function nameMustBeString(): string
    {
        return sprintf(self::FIELD_TYPE, 'name', 'string');
    }
}
