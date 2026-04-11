<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\ClubController;
use App\Http\Controllers\Api\ClubMemberController;
use App\Http\Controllers\Api\ClubRosterController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\DivisionController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MatchScheduleRequestController;
use App\Http\Controllers\Api\StadiumAuthController;
use App\Http\Controllers\Api\StadiumController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FriendshipController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.lang', 'api.key'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

        Route::prefix('stadium')->group(function () {
            Route::post('register', [StadiumAuthController::class, 'register']);
            Route::post('login', [StadiumAuthController::class, 'login']);
            Route::post('verify-otp', [StadiumAuthController::class, 'verifyOtp']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::apiResource('configs', ConfigController::class);
        Route::apiResource('areas', AreaController::class);

        Route::get('stadiums', [StadiumController::class, 'index']);
        Route::get('stadiums/{id}', [StadiumController::class, 'show'])->whereNumber('id');
    });

    Route::middleware(['auth:sanctum', 'player'])->group(function () {
        Route::get('profile', [UserController::class, 'me']);
        Route::put('update-profile', [UserController::class, 'updateProfile']);

        Route::apiResource('divisions', DivisionController::class);
        Route::apiResource('clubs', ClubController::class);

        Route::get('users/search', [FriendshipController::class, 'searchUsers']);
        Route::prefix('friends')->group(function () {
            Route::get('/', [FriendshipController::class, 'index']);
            Route::delete('unFriend/{friendUserId}', [FriendshipController::class, 'unfriend']);

            Route::post('requests', [FriendshipController::class, 'sendRequest']);
            Route::get('requests/incoming', [FriendshipController::class, 'incoming']);
            Route::get('requests/outgoing', [FriendshipController::class, 'outgoing']);
            Route::post('requests/{friendUserId}/accept', [FriendshipController::class, 'accept']);
            Route::post('requests/{friendUserId}/reject', [FriendshipController::class, 'reject']);
            Route::delete('requests/{friendUserId}', [FriendshipController::class, 'cancelOrDecline']);
        });

        Route::get('clubs/{club}/members', [ClubRosterController::class, 'members']);
        Route::post('clubs/{club}/invite-members', [ClubMemberController::class, 'invite']);
        Route::get('club-invites', [ClubMemberController::class, 'myInvites']);
        Route::post('club-invites/{clubMember}/accept', [ClubMemberController::class, 'accept']);
        Route::delete('club-invites/{clubMember}', [ClubMemberController::class, 'destroy']);

        Route::get('match-schedule-requests/recent', [MatchScheduleRequestController::class, 'recent']);
        Route::get('match-schedule-requests/nearby-pending-unpaired', [MatchScheduleRequestController::class, 'nearbyPendingUnpaired']);
        Route::get('match-schedule-requests', [MatchScheduleRequestController::class, 'index']);
        Route::post('match-schedule-requests', [MatchScheduleRequestController::class, 'store']);
        Route::get('match-schedule-requests/{matchScheduleRequest}', [MatchScheduleRequestController::class, 'show']);
        Route::post('match-schedule-requests/{matchScheduleRequest}/join', [MatchScheduleRequestController::class, 'join']);

        Route::get('matches/current', [MatchController::class, 'current']);
    });

    Route::middleware(['auth:sanctum', 'stadium_owner'])->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('stadium/profile', [StadiumAuthController::class, 'profile']);
        });

        Route::post('stadiums', [StadiumController::class, 'store']);
        Route::put('stadiums/{stadium}', [StadiumController::class, 'update']);
        Route::patch('stadiums/{stadium}', [StadiumController::class, 'update']);
        Route::delete('stadiums/{stadium}', [StadiumController::class, 'destroy']);

        Route::post('match-schedule-requests/{matchScheduleRequest}/accept-by-stadium', [MatchScheduleRequestController::class, 'acceptByStadium']);
        Route::post('matches/{match}/record-result', [MatchController::class, 'recordResult']);
    });
});
