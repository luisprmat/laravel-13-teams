<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admins can view team settings', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $this->actingAs($admin)
        ->get(route('team.settings'))
        ->assertOk()
        ->assertSee(__('Team Settings'));
});

test('members cannot view team settings', function () {
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);
    $member->switchTeam($team);

    $this->actingAs($member)
        ->get(route('team.settings'))
        ->assertForbidden();
});

test('admins can update team settings', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $this->actingAs($admin)
        ->put(route('team.settings.update'), [
            'description' => 'Our awesome team',
            'timezone' => 'Europe/Vilnius',
        ])
        ->assertRedirect(route('team.settings'));

    $team->refresh();
    expect($team->settings->get('description'))->toBe('Our awesome team');
    expect($team->settings->get('timezone'))->toBe('Europe/Vilnius');
});

test('admins can upload team avatar', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $this->actingAs($admin)
        ->post(route('team.settings.avatar'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 200, 200),
        ])
        ->assertRedirect(route('team.settings'));

    $team->refresh();
    expect($team->avatar_path)->not->toBeNull();
    Storage::disk('public')->assertExists($team->avatar_path);
});

test('old avatar is deleted when uploading new one', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $team = Team::factory()->create(['avatar_path' => 'team-avatars/old.jpg']);
    Storage::disk('public')->put('team-avatars/old.jpg', 'fake');
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $this->actingAs($admin)
        ->post(route('team.settings.avatar'), [
            'avatar' => UploadedFile::fake()->image('new.jpg', 200, 200),
        ]);

    Storage::disk('public')->assertMissing('team-avatars/old.jpg');
});

test('avatar validation rejects non-image files', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $this->actingAs($admin)
        ->post(route('team.settings.avatar'), [
            'avatar' => UploadedFile::fake()->create('document.pdf', 100),
        ])
        ->assertSessionHasErrors('avatar');
});

test('settings json column works with defaults', function () {
    $team = Team::factory()->create();

    expect($team->settings)->toBeNull();

    $team->update(['settings' => collect(['timezone' => 'UTC'])]);
    $team->refresh();

    expect($team->settings->get('timezone'))->toBe('UTC');
    expect($team->settings->get('description', ''))->toBe('');
});
