<?php

declare(strict_types=1);

namespace App\Messages\User;

use App\Enums\Auth\PasswordEnum;
use App\Messages\BaseMessage;

abstract class UserPasswordMessage extends BaseMessage
{
    public static function passwordIsRequired(): string
    {
        return sprintf(self::FIELD_REQUIRED, 'password');
    }

    public static function passwordMinLength(): string
    {
        return sprintf(self::FIELD_MIN_LENGTH, 'password', PasswordEnum::MIN_LENGTH->value);
    }

    public static function passwordNumbers(): string
    {
        return "O campo 'password' deve conter pelo menos um número";
    }

    public static function passwordSymbols(): string
    {
        return "O campo 'password' deve conter pelo menos um símbolo";
    }

    public static function passwordMixedCase(): string
    {
        return "O campo 'password' deve conter pelo menos uma letra maiúscula e uma letra minúscula.";
    }
}
