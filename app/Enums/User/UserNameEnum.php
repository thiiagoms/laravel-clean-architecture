<?php

declare(strict_types=1);

namespace App\Enums\User;

enum UserNameEnum: int
{
    case MIN_LENGTH = 3;
    case MAX_LENGTH = 250;
}
