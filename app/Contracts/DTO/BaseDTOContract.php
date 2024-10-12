<?php

declare(strict_types=1);

namespace App\Contracts\DTO;

interface BaseDTOContract
{
    public function toArray(): array;
}
