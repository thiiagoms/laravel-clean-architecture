<?php

declare(strict_types=1);

namespace App\Domain\Entity\User\ValueObject;

use App\Support\Sanitizer;

final readonly class Email
{
    private string $email;

    public function __construct(string $email)
    {
        $email = Sanitizer::clean($email);

        $this->validate($email);

        $this->email = $email;
    }

    public function getValue(): string
    {
        return $this->email;
    }

    public function equals(Email $email): bool
    {
        return $this->email === $email->getValue();
    }

    private function validate(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException("Invalid e-mail address given: '{$email}'");
        }
    }
}
