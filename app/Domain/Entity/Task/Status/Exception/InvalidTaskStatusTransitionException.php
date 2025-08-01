<?php

namespace App\Domain\Entity\Task\Status\Exception;

use App\Domain\Entity\Task\Interface\TaskOwnerInterface;
use App\Domain\Entity\Task\Status\Status;

class InvalidTaskStatusTransitionException extends \DomainException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(Status $from, Status $to, TaskOwnerInterface $owner): self
    {
        $message = strtr(
            "Invalid status transition from '{from}' to '{to}' for task owned by {owner}.",
            [
                '{from}' => $from->value,
                '{to}' => $to->value,
                '{owner}' => $owner->getName()->getValue(),
            ]
        );

        return new self($message);
    }
}
