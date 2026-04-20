<?php

namespace App\Enums;

enum TeamRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Editor = 'editor';
    case Member = 'member';
    case Viewer = 'viewer';

    /**
     * Get the display label for the role.
     */
    public function label(): string
    {
        return __('_teams.role.'.$this->value);
    }

    /**
     * Get all the permissions for this role.
     *
     * @return array<TeamPermission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Owner => TeamPermission::cases(),
            self::Admin => [
                TeamPermission::UpdateTeam,
                TeamPermission::CreateInvitation,
                TeamPermission::CancelInvitation,
                TeamPermission::CreateCategory,
                TeamPermission::UpdateCategory,
                TeamPermission::DeleteCategory,
                TeamPermission::CreatePost,
                TeamPermission::UpdatePost,
                TeamPermission::DeletePost,
            ],
            self::Editor => [
                TeamPermission::CreateInvitation,
                TeamPermission::CreateCategory,
                TeamPermission::UpdateCategory,
                TeamPermission::CreatePost,
                TeamPermission::UpdatePost,
            ],
            self::Member => [
                TeamPermission::CreateCategory,
                TeamPermission::UpdateCategory,
                TeamPermission::CreatePost,
                TeamPermission::UpdatePost,
            ],
            self::Viewer => [],
        };
    }

    /**
     * Determine if the role has the given permission.
     */
    public function hasPermission(TeamPermission $permission): bool
    {
        return in_array($permission, $this->permissions());
    }

    /**
     * Get the hierarchy level for this role.
     * Higher numbers indicate higher privileges.
     */
    public function level(): int
    {
        return match ($this) {
            self::Owner => 50,
            self::Admin => 40,
            self::Editor => 30,
            self::Member => 20,
            self::Viewer => 10,
        };
    }

    /**
     * Check if this role is at least as privileged as another role.
     */
    public function isAtLeast(TeamRole $role): bool
    {
        return $this->level() >= $role->level();
    }

    /**
     * Get the roles that can be assigned to team members (excludes Owner).
     *
     * @return array<array{value: string, label: string}>
     */
    public static function assignable(): array
    {
        return collect(self::cases())
            ->filter(fn (self $role) => $role !== self::Owner)
            ->map(fn (self $role) => ['value' => $role->value, 'label' => $role->label()])
            ->values()
            ->toArray();
    }
}
