<?php

namespace App\Presentation\Http\Api\V1\User\Rules;

use App\Domain\Entity\User\ValueObject\Password;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordIsValidRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            new Password($value);
        } catch (\InvalidArgumentException|\TypeError $e) {
            $fail('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.');
        }
    }
}
