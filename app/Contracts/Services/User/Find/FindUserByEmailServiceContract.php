<?php

declare(strict_types=1);

namespace App\Contracts\Services\User\Find;

use App\Exceptions\LogicalException;
use App\Models\User;

interface FindUserByEmailServiceContract
{
    /**
     * @throws LogicalException
     */
    public function handle(string $email): ?User;
}
