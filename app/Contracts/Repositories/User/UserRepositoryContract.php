<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\User;

use App\Contracts\Repositories\ReadableRepositoryContract;
use App\Contracts\Repositories\WritableRepositoryContract;
use App\Models\User;

interface UserRepositoryContract extends ReadableRepositoryContract, WritableRepositoryContract
{
    public function findByEmail(string $email): User|bool;
}
