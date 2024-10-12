<?php

declare(strict_types=1);

namespace App\Validators\Email;

use App\Contracts\Validators\Email\EmailValidatorContract;
use App\Exceptions\LogicalException;
use App\Messages\User\UserEmailMessage;

class EmailValidator implements EmailValidatorContract
{
    public function checkEmailIsValid(string $email): bool
    {
        throw_if(! isEmail($email), new LogicalException(UserEmailMessage::emailIsInvalid()));

        return true;
    }
}
