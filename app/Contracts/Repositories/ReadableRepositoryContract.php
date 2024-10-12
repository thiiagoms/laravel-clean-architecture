<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface ReadableRepositoryContract
{
    public function all(): Collection;

    public function find(string $id): mixed;
}
