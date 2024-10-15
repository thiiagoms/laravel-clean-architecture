<?php

declare(strict_types=1);

namespace App\Contracts\Services\Task\Find;

use App\Exceptions\LogicalException;
use App\Models\Task;
use DomainException;

interface FindTaskByIdServiceContract
{
    /**
     * @throws DomainException
     * @throws LogicalException
     */
    public function handle(string $id): Task;
}
