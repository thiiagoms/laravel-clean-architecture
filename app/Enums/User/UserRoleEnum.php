<?php

declare(strict_types=1);

namespace App\Enums\User;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
}
