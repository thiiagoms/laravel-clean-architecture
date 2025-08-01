<?php

namespace App\Domain\Entity\Task\ValueObject;

use App\Infrastructure\Support\Sanitizer;

class Title
{
    private string $title;

    private const int MAX_LENGTH = 100;

    public function __construct(string $title)
    {
        $title = Sanitizer::clean($title);

        $this->validate($title);

        $this->title = $title;
    }

    public function getValue(): string
    {
        return $this->title;
    }

    private function validate(string $title): void
    {
        if (strlen($title) > self::MAX_LENGTH) {
            $message = sprintf('Title cannot be longer than %d characters.', self::MAX_LENGTH);
            throw new \InvalidArgumentException($message);
        }
    }
}
