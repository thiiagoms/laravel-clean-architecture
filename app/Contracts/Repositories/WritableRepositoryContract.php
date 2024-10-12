<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

interface WritableRepositoryContract
{
    public function create(array $params): mixed;

    public function update(string $id, array $params): bool;

    public function destroy(string $id): bool;
}
