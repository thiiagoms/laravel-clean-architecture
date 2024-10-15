<?php

declare(strict_types=1);

namespace App\Contracts\Services\Task\Destroy;

use App\Exceptions\LogicalException;
use DomainException;

interface DestroyTaskServiceContract
{
    /**
     * @throws DomainException
     * @throws LogicalException
     */
    public function handle(string $id): bool;
}
