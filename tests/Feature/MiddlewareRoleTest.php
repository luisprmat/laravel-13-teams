<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

test('members get 403 on admin routes', function () {
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);
    $member->switchTeam($team);

    $this->actingAs($member)
        ->get(route('team.settings'))
        ->assertForbidden();
});

test('editors get 403 on admin routes', function () {
    $editor = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($editor, ['role' => TeamRole::Editor->value]);
    $editor->switchTeam($team);

    $this->actingAs($editor)
        ->get(route('team.settings'))
        ->assertForbidden();
});

test('admins can access admin routes', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $this->actingAs($admin)
        ->get(route('team.settings'))
        ->assertOk();
});

test('owners can access admin routes', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;

    $this->actingAs($owner)
        ->get(route('team.settings'))
        ->assertOk();
});

test('admins get 403 on owner-only routes', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $this->actingAs($admin)
        ->delete(route('team.destroy'))
        ->assertForbidden();
});

test('owners can delete non-personal teams', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $owner->switchTeam($team);

    $this->actingAs($owner)
        ->delete(route('team.destroy'))
        ->assertRedirect(route('teams.index'));

    $this->assertSoftDeleted('teams', ['id' => $team->id]);
});

test('owners cannot delete personal teams', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;

    $this->actingAs($owner)
        ->delete(route('team.destroy'))
        ->assertForbidden();
});
