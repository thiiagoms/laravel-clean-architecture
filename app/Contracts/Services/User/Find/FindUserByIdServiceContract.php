<?php

declare(strict_types=1);

namespace App\Contracts\Services\User\Find;

use App\Models\User;

interface FindUserByIdServiceContract
{
    public function handle(string $id): User|bool;
}
