<?php

use App\Enums\TeamPermission;
use App\Enums\TeamRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\Team;
use App\Models\User;

test('role hierarchy is correct', function () {
    expect(TeamRole::Owner->isAtLeast(TeamRole::Admin))->toBeTrue();
    expect(TeamRole::Admin->isAtLeast(TeamRole::Editor))->toBeTrue();
    expect(TeamRole::Editor->isAtLeast(TeamRole::Member))->toBeTrue();
    expect(TeamRole::Member->isAtLeast(TeamRole::Viewer))->toBeTrue();

    expect(TeamRole::Viewer->isAtLeast(TeamRole::Member))->toBeFalse();
    expect(TeamRole::Member->isAtLeast(TeamRole::Editor))->toBeFalse();
    expect(TeamRole::Editor->isAtLeast(TeamRole::Admin))->toBeFalse();
});

test('editor permissions include invitation:create but not team:update', function () {
    expect(TeamRole::Editor->hasPermission(TeamPermission::CreateInvitation))->toBeTrue();
    expect(TeamRole::Editor->hasPermission(TeamPermission::UpdateTeam))->toBeFalse();
});

test('viewer permissions are empty', function () {
    expect(TeamRole::Viewer->permissions())->toBeEmpty();
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

test('viewers cannot create categories', function () {
    $viewer = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($viewer, ['role' => TeamRole::Viewer->value]);
    $viewer->switchTeam($team);

    $this->actingAs($viewer)
        ->post(route('categories.store'), ['name' => 'Test'])
        ->assertForbidden();
});

test('viewers cannot update posts', function () {
    $viewer = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($viewer, ['role' => TeamRole::Viewer->value]);
    $viewer->switchTeam($team);

    $category = Category::factory()->create(['team_id' => $team->id]);
    $post = Post::factory()->create(['team_id' => $team->id, 'category_id' => $category->id]);

    $this->actingAs($viewer)
        ->put(route('posts.update', $post), [
            'category_id' => $category->id,
            'title' => 'Updated',
            'post_text' => 'Updated content.',
        ])
        ->assertForbidden();
});

test('members can still create categories but can not update them', function () {
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);
    $member->switchTeam($team);

    $this->actingAs($member)
        ->post(route('categories.store'), ['name' => 'Member Category'])
        ->assertRedirect(route('categories.index'));

    $category = Category::where('name', 'Member Category')->first();

    $this->actingAs($member)
        ->put(route('categories.update', $category), ['name' => 'Updated by Member'])
        ->assertForbidden();

    expect($category->fresh()->name)->toBe('Member Category');
});

test('editors can still create and update categories', function () {
    $editor = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($editor, ['role' => TeamRole::Editor->value]);
    $editor->switchTeam($team);

    $this->actingAs($editor)
        ->post(route('categories.store'), ['name' => 'Editor Category'])
        ->assertRedirect(route('categories.index'));

    $category = Category::where('name', 'Editor Category')->first();

    $this->actingAs($editor)
        ->put(route('categories.update', $category), ['name' => 'Updated by Editor'])
        ->assertRedirect(route('categories.index'));

    expect($category->fresh()->name)->toBe('Updated by Editor');
});

test('new roles appear in assignable list, owner does not', function () {
    $values = collect(TeamRole::assignable())->pluck('value')->toArray();

    expect($values)->toContain('editor');
    expect($values)->toContain('viewer');
    expect($values)->not->toContain('owner');
});
