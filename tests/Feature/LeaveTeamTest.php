<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

test('members can leave a team', function () {
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);
    $member->switchTeam($team);

    $this->actingAs($member)
        ->post(route('team.leave'))
        ->assertRedirect(route('teams.index'));

    expect($member->belongsToTeam($team))->toBeFalse();
});

test('user is switched to fallback team after leaving', function () {
    $member = User::factory()->create();
    $personalTeam = $member->currentTeam;

    $team = Team::factory()->create();
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);
    $member->switchTeam($team);

    $this->actingAs($member)
        ->post(route('team.leave'));

    expect($member->fresh()->current_team_id)->not->toBe($team->id);
});

test('owners cannot leave their team', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $owner->switchTeam($team);

    $this->actingAs($owner)
        ->post(route('team.leave'))
        ->assertForbidden();

    expect($owner->belongsToTeam($team))->toBeTrue();
});

test('users cannot leave personal teams', function () {
    $user = User::factory()->create();
    $personalTeam = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('team.leave'))
        ->assertForbidden();
});

test('admins can leave a team', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $this->actingAs($admin)
        ->post(route('team.leave'))
        ->assertRedirect(route('teams.index'));

    expect($admin->belongsToTeam($team))->toBeFalse();
});

test('leave team policy returns false for owners', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;

    expect($owner->can('leave', $team))->toBeFalse();
});

test('leave team policy returns true for members on non-personal teams', function () {
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    expect($member->can('leave', $team))->toBeTrue();
});
