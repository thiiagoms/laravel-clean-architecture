<?php

declare(strict_types=1);

namespace App\Domain\Common\ValueObject;

final readonly class Id
{
    private string $id;

    public function __construct(string $id)
    {
        $this->validate($id);

        $this->id = $id;
    }

    public function getValue(): string
    {
        return $this->id;
    }

    private function validate(string $id): void
    {
        $idIsValid = (bool) preg_match(
            pattern: '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/implementation',
            subject: $id
        );

        if ($idIsValid === false) {
            throw new \InvalidArgumentException("Invalid id given: '{$id}'");
        }
    }
}
