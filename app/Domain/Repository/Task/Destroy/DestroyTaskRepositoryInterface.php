<?php

namespace App\Domain\Repository\Task\Destroy;

use App\Domain\Common\ValueObject\Id;

interface DestroyTaskRepositoryInterface
{
    public function destroy(Id $id): bool;
}
