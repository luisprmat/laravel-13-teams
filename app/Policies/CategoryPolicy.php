<?php

namespace App\Policies;

use App\Enums\TeamPermission;
use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->current_team_id === $category->team_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasTeamPermission($user->currentTeam, TeamPermission::CreateCategory);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): bool
    {
        if ($user->current_team_id !== $category->team_id) {
            return false;
        }

        return $user->hasTeamPermission($user->currentTeam, TeamPermission::UpdateCategory);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): bool
    {
        if ($user->current_team_id !== $category->team_id) {
            return false;
        }

        return $user->hasTeamPermission($user->currentTeam, TeamPermission::DeleteCategory);
    }
}
