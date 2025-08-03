<?php

namespace App\Infrastructure\Policies\Task;

use App\Infrastructure\Persistence\Model\Task as LaravelTaskModel;
use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TaskPolicy
{
    public function viewAny(LaravelTaskModel $task, LaravelUserModel $user): bool
    {
        if ($user->id === $task->user->id) {
            return true;
        }

        throw new AccessDeniedHttpException('oops! It looks like you don’t have access to this resource.');
    }

    public function view(LaravelUserModel $user, LaravelTaskModel $task): bool
    {
        if ($user->id === $task->user->id) {
            return true;
        }

        throw new AccessDeniedHttpException('oops! It looks like you don’t have access to this resource.');
    }

    public function update(LaravelUserModel $user, LaravelTaskModel $task): bool
    {
        if ($user->id === $task->user->id) {
            return true;
        }

        throw new AccessDeniedHttpException('oops! It looks like you don’t have access to this resource.');
    }

    public function delete(LaravelUserModel $user, LaravelTaskModel $task): bool
    {
        if ($user->id === $task->user->id) {
            return true;
        }

        throw new AccessDeniedHttpException('oops! It looks like you don’t have access to this resource.');
    }
}
