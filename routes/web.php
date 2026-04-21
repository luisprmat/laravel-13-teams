<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeamController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('posts', PostController::class)->except(['show']);

        Route::post('leave', [TeamController::class, 'leave'])->name('team.leave');

        Route::middleware('team.member:admin')->group(function () {
            Route::view('settings', 'team-settings')->name('team.settings');
        });

        Route::middleware('team.member:owner')->group(function () {
            Route::delete('delete', [TeamController::class, 'destroy'])->name('team.destroy');
        });
    });

Route::get('join/{code}', [TeamController::class, 'join'])->name('team.join');

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

require __DIR__.'/settings.php';
