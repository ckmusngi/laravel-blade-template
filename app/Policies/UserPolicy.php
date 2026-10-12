<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view the list of users.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the given user.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->is($model);
    }

    /**
     * Determine whether the user can change the given user's role.
     *
     * Admins cannot change their own role, so the app always keeps at least one admin.
     */
    public function updateRole(User $user, User $model): bool
    {
        return $user->isAdmin() && ! $user->is($model);
    }

    /**
     * Determine whether the user can delete the given user.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && ! $user->is($model);
    }
}
