<?php

declare(strict_types=1);

namespace App\Contracts\Validators\Email;

use App\Exceptions\LogicalException;

interface EmailValidatorContract
{
    /**
     * @throws LogicalException
     */
    public function checkEmailIsValid(string $email): bool;
}
