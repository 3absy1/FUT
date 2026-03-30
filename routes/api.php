<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClubController;
use App\Http\Controllers\Api\ClubMemberController;
use App\Http\Controllers\Api\ClubRosterController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MatchScheduleRequestController;
use App\Http\Controllers\Api\StadiumController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FriendshipController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.lang', 'api.key'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    });
        Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('profile', [UserController::class, 'me']);
        Route::put('update-profile', [UserController::class, 'updateProfile']);

        Route::apiResource('stadiums', StadiumController::class);
        Route::apiResource('clubs', ClubController::class);

        // Friendships (friend cycle)
        Route::get('users/search', [FriendshipController::class, 'searchUsers']);
        Route::prefix('friends')->group(function () {
            // Friends
            Route::get('/', [FriendshipController::class, 'index']);
            Route::delete('unFriend/{friendUserId}', [FriendshipController::class, 'unfriend']);

            // Friend requests
            Route::post('requests', [FriendshipController::class, 'sendRequest']);
            Route::get('requests/incoming', [FriendshipController::class, 'incoming']);
            Route::get('requests/outgoing', [FriendshipController::class, 'outgoing']);
            Route::post('requests/{friendUserId}/accept', [FriendshipController::class, 'accept']);
            Route::post('requests/{friendUserId}/reject', [FriendshipController::class, 'reject']);
            Route::delete('requests/{friendUserId}', [FriendshipController::class, 'cancelOrDecline']);
        });

        // Club roster (select from club) and invitations
        Route::get('clubs/{club}/members', [ClubRosterController::class, 'members']); // optional q=
        Route::post('clubs/{club}/invite-members', [ClubMemberController::class, 'invite']);
        Route::get('club-invites', [ClubMemberController::class, 'myInvites']);
        Route::post('club-invites/{clubMember}/accept', [ClubMemberController::class, 'accept']);
        Route::delete('club-invites/{clubMember}', [ClubMemberController::class, 'destroy']);

        // Match schedule requests (assemble squad + multi schedule slots)
        Route::get('match-schedule-requests/recent', [MatchScheduleRequestController::class, 'recent']);
        Route::get('match-schedule-requests/nearby-pending-unpaired', [MatchScheduleRequestController::class, 'nearbyPendingUnpaired']);
        Route::get('match-schedule-requests', [MatchScheduleRequestController::class, 'index']);
        Route::post('match-schedule-requests', [MatchScheduleRequestController::class, 'store']);
        Route::get('match-schedule-requests/{matchScheduleRequest}', [MatchScheduleRequestController::class, 'show']);
        Route::post('match-schedule-requests/{matchScheduleRequest}/join', [MatchScheduleRequestController::class, 'join']);
        Route::post('match-schedule-requests/{matchScheduleRequest}/accept-by-stadium', [MatchScheduleRequestController::class, 'acceptByStadium']);

        // Matches (record result by stadium owner)
        Route::get('matches/current', [MatchController::class, 'current']);
        Route::post('matches/{match}/record-result', [MatchController::class, 'recordResult']);

        });
});
