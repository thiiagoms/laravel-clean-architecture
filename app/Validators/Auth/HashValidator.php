<?php

declare(strict_types=1);

namespace App\Validators\Auth;

use App\Contracts\Validators\Auth\HashValidatorContract;
use Illuminate\Support\Facades\Hash;

class HashValidator implements HashValidatorContract
{
    public function checkPasswordHashMatch(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }
}
