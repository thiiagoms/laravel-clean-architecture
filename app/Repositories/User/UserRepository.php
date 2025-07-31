<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Infrastructure\Persistence\Model\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryContract
{
    /** @var User */
    protected $model = User::class;

    public function findByEmail(string $email): User|bool
    {
        $user = $this->model->where('email', $email)->first();

        return ! is_null($user) ? $user : false;
    }
}
