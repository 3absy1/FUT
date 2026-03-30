<?php

namespace App\Repositories\Friendship;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FriendshipRepositoryInterface
{
    public function searchUsers(string $q, int $excludeUserId): LengthAwarePaginator;

    public function sendRequest(User $requester, int $friendUserId): FriendRequest;

    public function accept(User $actor, int $friendUserId): FriendRequest;

    public function reject(User $actor, int $friendUserId): FriendRequest;

    public function cancelOrDecline(User $actor, int $friendUserId): void;

    public function unfriend(User $actor, int $friendUserId): void;

    public function listFriends(User $user, ?string $q = null): LengthAwarePaginator;

    public function listIncomingRequests(User $user): LengthAwarePaginator;

    public function listOutgoingRequests(User $user): LengthAwarePaginator;
}

