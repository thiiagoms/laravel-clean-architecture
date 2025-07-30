<?php

declare(strict_types=1);

namespace App\Domain\Entity\User\ValueObject;

use App\Support\Sanitizer;
use InvalidArgumentException;

final readonly class Name
{
    private const int MIN_LENGTH = 3;

    private const int MAX_LENGTH = 150;

    private string $name;

    public function __construct(string $name)
    {
        $name = Sanitizer::clean($name);

        $this->validate($name);

        $this->name = $this->transform($name);
    }

    public function getValue(): string
    {
        return $this->name;
    }

    private function validate(array|string $name): void
    {
        $this->validateLength($name);
        $this->validateOnlyLetters($name);
    }

    private function validateLength(string $name): void
    {
        if (strlen($name) < self::MIN_LENGTH || strlen($name) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Name must be between %d and %d characters.', self::MIN_LENGTH, self::MAX_LENGTH)
            );
        }
    }

    private function validateOnlyLetters(string $name): void
    {
        $pattern = (bool) preg_match('/^[\p{L}\p{M}\'\.\-\s]+$/u', $name);

        if ($pattern === false) {
            throw new InvalidArgumentException('Name must contains only letters.');
        }
    }

    private function transform(string $name): string
    {
        return ucwords(mb_strtolower($name));
    }
}
