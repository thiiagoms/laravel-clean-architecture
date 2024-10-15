<?php

declare(strict_types=1);

namespace App\Contracts\Services\Task\Find;

use App\Exceptions\LogicalException;
use DomainException;
use Illuminate\Database\Eloquent\Collection;

interface FindTasksByUserServiceContract
{
    /**
     * @throws DomainException
     * @throws LogicalException
     */
    public function handle(string $userId): Collection;
}
