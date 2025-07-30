<?php

namespace App\Domain\Entity\User\Role\Exception;

use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\User;

class InvalidRoleTransitionException extends \DomainException
{
    public function __construct(Role $from, Role $to, User $user)
    {
        $message = strtr("Invalid role transition from '{from}' to '{to}' on user '{email}'", [
            '{from}' => $from->value,
            '{to}' => $to->value,
            '{email}' => $user->getEmail()->getValue(),
        ]);

        parent::__construct($message);
    }
}
