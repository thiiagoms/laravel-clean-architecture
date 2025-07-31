<?php

namespace App\Application\UseCases\Auth\Exception;

class InvalidCredentialsException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(): self
    {
        return new self('Authentication failed. Please check your credentials.');
    }
}
