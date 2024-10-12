<?php

declare(strict_types=1);

namespace App\Messages\User;

use App\Messages\BaseMessage;

abstract class UserEmailMessage extends BaseMessage
{
    public static function emailIsRequired(): string
    {
        return sprintf(self::FIELD_REQUIRED, 'email');
    }

    public static function emailIsInvalid(): string
    {
        return sprintf(self::FIELD_TYPE, 'email', 'e-mail');
    }

    public static function emailAlreadyExists(): string
    {
        return sprintf(self::RECORD_ALREADY_EXISTS, 'email');
    }
}
