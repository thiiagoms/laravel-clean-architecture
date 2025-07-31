<?php

namespace App\Presentation\Api\V1\Common\Rules;

use App\Domain\Entity\User\ValueObject\Name;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NameIsValidRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            new Name($value);
        } catch (\InvalidArgumentException|\TypeError $e) {
            $fail('Name must be between 3 and 150 characters and contains only letters.');
        }
    }
}
