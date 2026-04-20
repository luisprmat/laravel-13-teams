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
}
