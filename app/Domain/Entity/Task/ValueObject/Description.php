<?php

namespace App\Domain\Entity\Task\ValueObject;

use App\Infrastructure\Support\Sanitizer;

class Description
{
    private string $description;

    public function __construct(string $description)
    {
        $description = Sanitizer::clean($description);

        $this->description = $description;
    }

    public function getValue(): string
    {
        return $this->description;
    }
}
