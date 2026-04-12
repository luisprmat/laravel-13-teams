<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Auth;

trait BelongsToTeam
{
    public static function bootBelongsToTeam(): void
    {
        static::creating(function ($model) {
            $model->team_id ??= Auth::user()?->current_team_id;
        });
    }
}
