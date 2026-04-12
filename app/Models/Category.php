<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use App\Models\Scopes\TeamScope;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_id', 'name', 'slug'])]
#[ScopedBy([TeamScope::class])]
class Category extends Model
{
    use BelongsToTeam;

    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
