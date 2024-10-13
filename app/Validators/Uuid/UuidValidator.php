<?php

declare(strict_types=1);

namespace App\Validators\Uuid;

use App\Contracts\Validators\Uuid\UuidValidatorContract;
use App\Exceptions\LogicalException;
use App\Messages\System\SystemMessage;

class UuidValidator implements UuidValidatorContract
{
    public function checkUuidIsValid(string $id): void
    {
        throw_if(! uuid_is_valid($id), new LogicalException(SystemMessage::INVALID_PARAMETER));
    }
}
