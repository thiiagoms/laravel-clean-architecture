<?php

namespace App\Infrastructure\Persistence\Repository\Task\Destroy;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Repository\Task\Destroy\DestroyTaskRepositoryInterface;
use App\Infrastructure\Persistence\Repository\Task\BaseTaskRepository;

class EloquentDestroyTaskByIdRepository extends BaseTaskRepository implements DestroyTaskRepositoryInterface
{
    public function destroy(Id $id): bool
    {
        return (bool) $this->model->destroy($id->getValue());
    }
}
