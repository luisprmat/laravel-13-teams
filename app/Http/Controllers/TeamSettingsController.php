<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeamSettingsController extends Controller
{
    public function edit(Team $currentTeam): View
    {
        return view('team-settings', [
            'team' => $currentTeam,
        ]);
    }

    public function update(Request $request, Team $currentTeam): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string', 'max:500'],
            'timezone' => ['nullable', 'string', 'max:100'],
        ]);

        $settings = $currentTeam->settings ?? collect();
        $settings->put('description', $validated['description'] ?? '');
        $settings->put('timezone', $validated['timezone'] ?? 'UTC');

        $currentTeam->update(['settings' => $settings]);

        return redirect()->route('team.settings')->with('success', __('Settings updated.'));
    }

    public function updateAvatar(Request $request, Team $currentTeam): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:1024'],
        ]);

        if ($currentTeam->avatar_path) {
            Storage::disk('public')->delete($currentTeam->avatar_path);
        }

        $path = $request->file('avatar')->store('team-avatars', 'public');
        $currentTeam->update(['avatar_path' => $path]);

        return redirect()->route('team.settings')->with('success', __('Avatar updated.'));
    }
}
