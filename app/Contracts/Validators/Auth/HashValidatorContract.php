<?php

declare(strict_types=1);

namespace App\Contracts\Validators\Auth;

interface HashValidatorContract
{
    public function checkPasswordHashMatch(string $password, string $hash): bool;
}
