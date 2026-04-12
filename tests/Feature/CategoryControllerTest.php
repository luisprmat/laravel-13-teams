<?php

use App\Enums\TeamRole;
use App\Models\Category;
use App\Models\Team;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->team = $this->user->currentTeam;
});

test('authenticated users can view categories index', function () {
    Category::factory()->create(['team_id' => $this->team->id]);

    $this->actingAs($this->user)
        ->get(route('categories.index'))
        ->assertOk()
        ->assertViewHas('categories');
});

test('categories are scoped to current team', function () {
    $ownCategory = Category::factory()->create(['team_id' => $this->team->id]);
    $otherCategory = Category::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('categories.index'));

    $categories = $response->viewData('categories');
    expect($categories->pluck('id')->toArray())->toContain($ownCategory->id);
    expect($categories->pluck('id')->toArray())->not->toContain($otherCategory->id);
});

test('authenticated users can create a category', function () {
    $this->actingAs($this->user)
        ->post(route('categories.store'), ['name' => 'Tech'])
        ->assertRedirect(route('categories.index'));

    $this->assertDatabaseHas('categories', [
        'team_id' => $this->team->id,
        'name' => 'Tech',
        'slug' => 'tech',
    ]);
});

test('authenticated users can update a category', function () {
    $category = Category::factory()->create(['team_id' => $this->team->id]);

    $this->actingAs($this->user)
        ->put(route('categories.update', $category), ['name' => 'Updated'])
        ->assertRedirect(route('categories.index'));

    expect($category->fresh()->name)->toBe('Updated');
});

test('members cannot delete categories', function () {
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);
    $member->switchTeam($team);

    $category = Category::factory()->create(['team_id' => $team->id]);

    $this->actingAs($member)
        ->delete(route('categories.destroy', $category))
        ->assertForbidden();
});

test('admins can delete categories', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $category = Category::factory()->create(['team_id' => $team->id]);

    $this->actingAs($admin)
        ->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'));

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('owners can delete categories', function () {
    $category = Category::factory()->create(['team_id' => $this->team->id]);

    $this->actingAs($this->user)
        ->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'));

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('store requires name', function () {
    $this->actingAs($this->user)
        ->post(route('categories.store'), ['name' => ''])
        ->assertSessionHasErrors('name');
});

test('BelongsToTeam trait auto-assigns team_id on create', function () {
    $this->actingAs($this->user);

    $category = Category::create([
        'name' => 'Auto Team',
        'slug' => 'auto-team',
    ]);

    expect($category->team_id)->toBe($this->team->id);
});
