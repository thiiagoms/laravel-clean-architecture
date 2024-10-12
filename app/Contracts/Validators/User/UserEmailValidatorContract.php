<?php

declare(strict_types=1);

namespace App\Contracts\Validators\User;

use App\Exceptions\LogicalException;
use DomainException;

interface UserEmailValidatorContract
{
    /**
     * @throws DomainException
     * @throws LogicalException
     */
    public function checkUserEmailIsAvailable(string $email): void;
}
