<?php

namespace App\Policies\Task;

use App\Exceptions\AuthorizationException;
use App\Messages\Auth\AuthMessage;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Task $task, User $user): bool
    {
        if ($user->id === $task->user->id || $user->isAdmin()) {
            return true;
        }

        throw new AuthorizationException(AuthMessage::UNAUTHORIZED);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->id === $task->user->id || $user->isAdmin()) {
            return true;
        }

        throw new AuthorizationException(AuthMessage::UNAUTHORIZED);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->id === $task->user->id || $user->isAdmin()) {
            return true;
        }

        throw new AuthorizationException(AuthMessage::UNAUTHORIZED);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->id === $task->user->id || $user->isAdmin()) {
            return true;
        }

        throw new AuthorizationException(AuthMessage::UNAUTHORIZED);
    }
}
