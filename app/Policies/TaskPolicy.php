<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view tasks
    }

    public function view(User $user, Task $task): bool
    {
        return $user->isAdmin() || $user->id === $task->assigned_to || $user->id === $task->created_by;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Task $task): bool
    {
        return $user->isAdmin() || $user->id === $task->assigned_to;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin() || $user->id === $task->created_by;
    }
}