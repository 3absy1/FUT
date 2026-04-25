<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ClubAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MatchAdminController;
use App\Http\Controllers\Admin\PaymentAdminController;
use App\Http\Controllers\Admin\StadiumAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ─── Admin Panel ────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth (guests only)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    // Protected admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('users', [UserAdminController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserAdminController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserAdminController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserAdminController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserAdminController::class, 'destroy'])->name('users.destroy');

        // Clubs
        Route::get('clubs', [ClubAdminController::class, 'index'])->name('clubs.index');
        Route::get('clubs/{club}', [ClubAdminController::class, 'show'])->name('clubs.show');
        Route::get('clubs/{club}/edit', [ClubAdminController::class, 'edit'])->name('clubs.edit');
        Route::put('clubs/{club}', [ClubAdminController::class, 'update'])->name('clubs.update');
        Route::delete('clubs/{club}', [ClubAdminController::class, 'destroy'])->name('clubs.destroy');

        // Stadiums
        Route::get('stadiums', [StadiumAdminController::class, 'index'])->name('stadiums.index');
        Route::get('stadiums/create', [StadiumAdminController::class, 'create'])->name('stadiums.create');
        Route::post('stadiums', [StadiumAdminController::class, 'store'])->name('stadiums.store');
        Route::get('stadiums/{stadium}', [StadiumAdminController::class, 'show'])->name('stadiums.show');
        Route::get('stadiums/{stadium}/edit', [StadiumAdminController::class, 'edit'])->name('stadiums.edit');
        Route::put('stadiums/{stadium}', [StadiumAdminController::class, 'update'])->name('stadiums.update');
        Route::delete('stadiums/{stadium}', [StadiumAdminController::class, 'destroy'])->name('stadiums.destroy');

        // Matches
        Route::get('matches', [MatchAdminController::class, 'index'])->name('matches.index');
        Route::get('matches/{match}', [MatchAdminController::class, 'show'])->name('matches.show');
        Route::get('matches/{match}/edit', [MatchAdminController::class, 'edit'])->name('matches.edit');
        Route::put('matches/{match}', [MatchAdminController::class, 'update'])->name('matches.update');
        Route::delete('matches/{match}', [MatchAdminController::class, 'destroy'])->name('matches.destroy');
        Route::get('matches/{match}/players', [MatchAdminController::class, 'players'])->name('matches.players');

        // Payments
        Route::get('payments', [PaymentAdminController::class, 'index'])->name('payments.index');
    });
});
