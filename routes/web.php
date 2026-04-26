<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AreaAdminController;
use App\Http\Controllers\Admin\ClubAdminController;
use App\Http\Controllers\Admin\ConfigAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DivisionAdminController;
use App\Http\Controllers\Admin\MatchAdminController;
use App\Http\Controllers\Admin\PaymentAdminController;
use App\Http\Controllers\Admin\StadiumAdminController;
use App\Http\Controllers\Admin\TournamentAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ─── Admin Panel ──────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth:admin')->group(function () {

        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Players
        Route::get('users',             [UserAdminController::class, 'index'])->name('users.index');
        Route::get('users/{user}',      [UserAdminController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserAdminController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}',      [UserAdminController::class, 'update'])->name('users.update');
        Route::delete('users/{user}',   [UserAdminController::class, 'destroy'])->name('users.destroy');

        // Clubs
        Route::get('clubs',             [ClubAdminController::class, 'index'])->name('clubs.index');
        Route::get('clubs/{club}',      [ClubAdminController::class, 'show'])->name('clubs.show');
        Route::get('clubs/{club}/edit', [ClubAdminController::class, 'edit'])->name('clubs.edit');
        Route::put('clubs/{club}',      [ClubAdminController::class, 'update'])->name('clubs.update');
        Route::delete('clubs/{club}',   [ClubAdminController::class, 'destroy'])->name('clubs.destroy');

        // Stadiums
        Route::get('stadiums',                [StadiumAdminController::class, 'index'])->name('stadiums.index');
        Route::get('stadiums/create',         [StadiumAdminController::class, 'create'])->name('stadiums.create');
        Route::post('stadiums',               [StadiumAdminController::class, 'store'])->name('stadiums.store');
        Route::get('stadiums/{stadium}',      [StadiumAdminController::class, 'show'])->name('stadiums.show');
        Route::get('stadiums/{stadium}/edit', [StadiumAdminController::class, 'edit'])->name('stadiums.edit');
        Route::put('stadiums/{stadium}',      [StadiumAdminController::class, 'update'])->name('stadiums.update');
        Route::delete('stadiums/{stadium}',   [StadiumAdminController::class, 'destroy'])->name('stadiums.destroy');

        // Matches
        Route::get('matches',               [MatchAdminController::class, 'index'])->name('matches.index');
        Route::get('matches/{match}',       [MatchAdminController::class, 'show'])->name('matches.show');
        Route::get('matches/{match}/edit',  [MatchAdminController::class, 'edit'])->name('matches.edit');
        Route::put('matches/{match}',       [MatchAdminController::class, 'update'])->name('matches.update');
        Route::delete('matches/{match}',    [MatchAdminController::class, 'destroy'])->name('matches.destroy');
        Route::get('matches/{match}/players',[MatchAdminController::class, 'players'])->name('matches.players');

        // Tournaments
        Route::get('tournaments',                   [TournamentAdminController::class, 'index'])->name('tournaments.index');
        Route::get('tournaments/create',            [TournamentAdminController::class, 'create'])->name('tournaments.create');
        Route::post('tournaments',                  [TournamentAdminController::class, 'store'])->name('tournaments.store');
        Route::get('tournaments/{tournament}',      [TournamentAdminController::class, 'show'])->name('tournaments.show');
        Route::get('tournaments/{tournament}/edit', [TournamentAdminController::class, 'edit'])->name('tournaments.edit');
        Route::put('tournaments/{tournament}',      [TournamentAdminController::class, 'update'])->name('tournaments.update');
        Route::delete('tournaments/{tournament}',   [TournamentAdminController::class, 'destroy'])->name('tournaments.destroy');

        // Areas
        Route::get('areas',             [AreaAdminController::class, 'index'])->name('areas.index');
        Route::get('areas/create',      [AreaAdminController::class, 'create'])->name('areas.create');
        Route::post('areas',            [AreaAdminController::class, 'store'])->name('areas.store');
        Route::get('areas/{area}',      [AreaAdminController::class, 'show'])->name('areas.show');
        Route::get('areas/{area}/edit', [AreaAdminController::class, 'edit'])->name('areas.edit');
        Route::put('areas/{area}',      [AreaAdminController::class, 'update'])->name('areas.update');
        Route::delete('areas/{area}',   [AreaAdminController::class, 'destroy'])->name('areas.destroy');

        // Divisions
        Route::get('divisions',                 [DivisionAdminController::class, 'index'])->name('divisions.index');
        Route::get('divisions/create',          [DivisionAdminController::class, 'create'])->name('divisions.create');
        Route::post('divisions',                [DivisionAdminController::class, 'store'])->name('divisions.store');
        Route::get('divisions/{division}/edit', [DivisionAdminController::class, 'edit'])->name('divisions.edit');
        Route::put('divisions/{division}',      [DivisionAdminController::class, 'update'])->name('divisions.update');
        Route::delete('divisions/{division}',   [DivisionAdminController::class, 'destroy'])->name('divisions.destroy');

        // Configs
        Route::get('configs',               [ConfigAdminController::class, 'index'])->name('configs.index');
        Route::get('configs/create',        [ConfigAdminController::class, 'create'])->name('configs.create');
        Route::post('configs',              [ConfigAdminController::class, 'store'])->name('configs.store');
        Route::get('configs/{config}/edit', [ConfigAdminController::class, 'edit'])->name('configs.edit');
        Route::put('configs/{config}',      [ConfigAdminController::class, 'update'])->name('configs.update');
        Route::delete('configs/{config}',   [ConfigAdminController::class, 'destroy'])->name('configs.destroy');

        // Payments
        Route::get('payments', [PaymentAdminController::class, 'index'])->name('payments.index');
    });
});
