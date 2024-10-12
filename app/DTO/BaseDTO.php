<?php

declare(strict_types=1);

namespace App\DTO;

use App\Contracts\DTO\BaseDTOContract;

class BaseDTO implements BaseDTOContract
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
