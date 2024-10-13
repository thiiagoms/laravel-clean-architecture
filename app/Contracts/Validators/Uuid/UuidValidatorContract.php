<?php

declare(strict_types=1);

namespace App\Contracts\Validators\Uuid;

use App\Exceptions\LogicalException;

interface UuidValidatorContract
{
    /**
     * @throws LogicalException
     */
    public function checkUuidIsValid(string $id): void;
}
