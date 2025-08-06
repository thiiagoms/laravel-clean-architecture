<?php

namespace App\Infrastructure\Persistence\Repository\User;

use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use App\Infrastructure\Persistence\Repository\BaseRepository;

abstract class BaseUserRepository extends BaseRepository
{
    /** @var LaravelUserModel */
    protected $model = LaravelUserModel::class;
}
