<?php

use App\Enums\TeamPermission;
use App\Enums\TeamRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\Team;
use App\Models\User;

test('editors can create categories', function () {
    $editor = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($editor, ['role' => TeamRole::Editor->value]);
    $editor->switchTeam($team);

    $this->actingAs($editor)
        ->post(route('categories.store'), ['name' => 'Editor Category'])
        ->assertRedirect(route('categories.index'));
});

test('editors cannot delete categories', function () {
    $editor = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($editor, ['role' => TeamRole::Editor->value]);
    $editor->switchTeam($team);

    $category = Category::factory()->create(['team_id' => $team->id]);

    $this->actingAs($editor)
        ->delete(route('categories.destroy', $category))
        ->assertForbidden();
});

test('editors cannot delete posts', function () {
    $editor = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($editor, ['role' => TeamRole::Editor->value]);
    $editor->switchTeam($team);

    $category = Category::factory()->create(['team_id' => $team->id]);
    $post = Post::factory()->create(['team_id' => $team->id, 'category_id' => $category->id]);

    $this->actingAs($editor)
        ->delete(route('posts.destroy', $post))
        ->assertForbidden();
});

test('viewers cannot create or update posts', function () {
    $viewer = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($viewer, ['role' => TeamRole::Viewer->value]);
    $viewer->switchTeam($team);

    $this->actingAs($viewer)
        ->post(route('posts.store'), [
            'category_id' => 1,
            'title' => 'Test',
            'post_text' => 'Content',
        ])
        ->assertForbidden();
});

test('hasTeamPermission returns correct values for editor', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Editor->value]);

    expect($user->hasTeamPermission($team, TeamPermission::CreateCategory))->toBeTrue();
    expect($user->hasTeamPermission($team, TeamPermission::UpdateCategory))->toBeTrue();
    expect($user->hasTeamPermission($team, TeamPermission::DeleteCategory))->toBeFalse();
    expect($user->hasTeamPermission($team, TeamPermission::CreatePost))->toBeTrue();
    expect($user->hasTeamPermission($team, TeamPermission::DeletePost))->toBeFalse();
});

test('admins have full resource permissions', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Admin->value]);

    expect($user->hasTeamPermission($team, TeamPermission::CreateCategory))->toBeTrue();
    expect($user->hasTeamPermission($team, TeamPermission::DeleteCategory))->toBeTrue();
    expect($user->hasTeamPermission($team, TeamPermission::CreatePost))->toBeTrue();
    expect($user->hasTeamPermission($team, TeamPermission::DeletePost))->toBeTrue();
});
