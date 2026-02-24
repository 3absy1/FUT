<?php

namespace App\Repositories\Friendship;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FriendshipRepositoryInterface
{
    public function searchUsers(string $q, int $excludeUserId): LengthAwarePaginator;

    public function sendRequest(User $requester, int $friendUserId): Friendship;

    public function accept(User $actor, Friendship $friendship): Friendship;

    public function delete(User $actor, Friendship $friendship): void;

    public function listFriends(User $user, ?string $q = null): LengthAwarePaginator;

    public function listIncomingRequests(User $user): LengthAwarePaginator;

    public function listOutgoingRequests(User $user): LengthAwarePaginator;
}

