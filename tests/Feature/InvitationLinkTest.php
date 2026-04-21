<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

test('join link redirects authenticated users to accept page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $invitation = $team->invitations()->create([
        'email' => $user->email,
        'role' => TeamRole::Member,
        'invited_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('team.join', $invitation->code))
        ->assertRedirect(route('invitations.accept', $invitation));
});

test('join link redirects guests to registration', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();

    $invitation = $team->invitations()->create([
        'email' => 'newuser@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $owner->id,
    ]);

    $this->get(route('team.join', $invitation->code))
        ->assertRedirect(route('register'));

    expect(session('pending_invitation'))->toBe($invitation->code);
});

test('join link returns 404 for accepted invitations', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();

    $invitation = $team->invitations()->create([
        'email' => 'someone@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $owner->id,
        'accepted_at' => now(),
    ]);

    $this->get(route('team.join', $invitation->code))
        ->assertNotFound();
});

test('join link returns 404 for expired invitations', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();

    $invitation = $team->invitations()->create([
        'email' => 'someone@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $owner->id,
        'expires_at' => now()->subDay(),
    ]);

    $this->get(route('team.join', $invitation->code))
        ->assertNotFound();
});

test('join link returns 404 for invalid codes', function () {
    $this->get(route('team.join', 'nonexistent-code'))
        ->assertNotFound();
});

test('pending invitation is accepted after registration', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();

    $invitation = $team->invitations()->create([
        'email' => 'newuser@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $owner->id,
    ]);

    $this->withSession(['pending_invitation' => $invitation->code])
        ->post('/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $newUser = User::where('email', 'newuser@example.com')->first();

    expect($newUser->belongsToTeam($team))->toBeTrue();
    expect($invitation->fresh()->isAccepted())->toBeTrue();
    expect($newUser->current_team_id)->toBe($team->id);
});
