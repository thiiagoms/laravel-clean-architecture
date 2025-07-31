<?php

declare(strict_types=1);

namespace App\Domain\Entity\Auth\Token;

final readonly class Token
{
    public function __construct(
        private string $token,
        private string $type,
        private int $expiresIn,
    ) {
        $this->validate();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    private function validate(): void
    {
        foreach (get_object_vars($this) as $property => $value) {
            if (empty($value)) {
                throw new \InvalidArgumentException("Property '{$property}' cannot be empty.");
            }
        }
    }
}
