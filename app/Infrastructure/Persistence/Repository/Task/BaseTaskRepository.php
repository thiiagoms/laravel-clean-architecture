<?php

namespace App\Infrastructure\Persistence\Repository\Task;

use App\Infrastructure\Persistence\Model\Task as LaravelTaskModel;
use App\Infrastructure\Persistence\Repository\BaseRepository;

abstract class BaseTaskRepository extends BaseRepository
{
    /** @var LaravelTaskModel */
    protected $model = LaravelTaskModel::class;
}
