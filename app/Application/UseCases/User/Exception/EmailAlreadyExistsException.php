<?php

namespace App\Application\UseCases\User\Exception;

class EmailAlreadyExistsException extends \DomainException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(): self
    {
        return new self('User with provided e-mail already exists');
    }
}
