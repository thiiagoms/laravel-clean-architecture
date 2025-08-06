<?php

declare(strict_types=1);

namespace App\Application\UseCases\Task\Destroy;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Repository\Task\Destroy\DestroyTaskRepositoryInterface;

final readonly class DestroyTask
{
    public function __construct(private readonly DestroyTaskRepositoryInterface $repository) {}

    public function handle(Id $id): bool
    {
        return $this->repository->destroy($id);
    }
}
