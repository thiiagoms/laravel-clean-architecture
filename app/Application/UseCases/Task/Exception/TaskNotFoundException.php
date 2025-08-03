<?php

namespace App\Application\UseCases\Task\Exception;

class TaskNotFoundException extends \DomainException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(): self
    {
        return new self(message: 'Task not found.');
    }
}
