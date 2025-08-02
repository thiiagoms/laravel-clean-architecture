<?php

namespace App\Presentation\Http\Api\V1\User\Rules;

use App\Domain\Entity\User\ValueObject\Email;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailIsValidRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            new Email($value);
        } catch (\InvalidArgumentException|\TypeError $e) {
            $fail('The provided email address is not valid. Please enter a valid email.');
        }
    }
}
