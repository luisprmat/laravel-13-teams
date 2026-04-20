<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;

class TeamController extends Controller
{
    public function destroy(Team $currentTeam): RedirectResponse
    {
        abort_if($currentTeam->is_personal, 403, __('Personal teams cannot be deleted.'));

        $user = request()->user();

        $currentTeam->delete();

        $fallback = $user->fallbackTeam();

        if ($fallback) {
            $user->switchTeam($fallback);
        }

        return redirect()->route('teams.index')->with('success', __('Team deleted.'));
    }

    public function leave(Team $currentTeam): RedirectResponse
    {
        $user = request()->user();

        abort_if($user->ownsTeam($currentTeam), 403, __('Owners cannot leave. Transfer ownership first.'));
        abort_if($currentTeam->is_personal, 403, __('You cannot leave a personal team.'));

        $currentTeam->memberships()->where('user_id', $user->id)->delete();

        $fallback = $user->fallbackTeam($currentTeam);

        if ($fallback) {
            $user->switchTeam($fallback);
        }

        return redirect()->route('teams.index')->with('success', __('You have left the team.'));
    }
}
