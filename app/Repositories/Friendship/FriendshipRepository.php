<?php

namespace App\Repositories\Friendship;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FriendshipRepository implements FriendshipRepositoryInterface
{
    public function searchUsers(string $q, int $excludeUserId): LengthAwarePaginator
    {
        return User::query()
            ->where('id', '!=', $excludeUserId)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('nick_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function sendRequest(User $requester, int $friendUserId): FriendRequest
    {
        if ($requester->id === $friendUserId) {
            throw ValidationException::withMessages([
                'friend_user_id' => [__('api.friendship.cannot_friend_self')],
            ]);
        }

        $friend = User::findOrFail($friendUserId);
        $requesterId = (int) $requester->id;
        $otherUserId = (int) $friend->id;

        $alreadyFriends = Friend::query()
            ->where('user_id', $requesterId)
            ->where('friend_id', $otherUserId)
            ->exists();

        if ($alreadyFriends) {
            throw ValidationException::withMessages([
                'friend_user_id' => [__('api.friendship.already_friends')],
            ]);
        }

        $incomingPending = FriendRequest::query()
            ->where('sender_user_id', $otherUserId)
            ->where('receiver_user_id', $requesterId)
            ->where('status', 'pending')
            ->exists();

        if ($incomingPending) {
            throw ValidationException::withMessages([
                'friend_user_id' => [__('api.friendship.request_already_pending')],
            ]);
        }

        return FriendRequest::updateOrCreate(
            ['sender_user_id' => $requesterId, 'receiver_user_id' => $otherUserId],
            [
                'status' => 'pending',
                'accepted_at' => null,
                'rejected_at' => null,
            ]
        );
    }

    public function accept(User $actor, int $friendUserId): FriendRequest
    {
        $actorId = (int) $actor->id;
        $friendUserId = (int) $friendUserId;

        $incoming = FriendRequest::query()
            ->where('sender_user_id', $friendUserId)
            ->where('receiver_user_id', $actorId)
            ->firstOrFail();

        if ($incoming->status !== 'pending') {
            throw ValidationException::withMessages([
                'friendship' => [__('api.friendship.not_pending')],
            ]);
        }

        return DB::transaction(function () use ($actorId, $friendUserId) {
            $now = now();

            Friend::updateOrCreate(
                ['user_id' => $actorId, 'friend_id' => $friendUserId],
                ['updated_at' => $now, 'created_at' => $now]
            );

            Friend::updateOrCreate(
                ['user_id' => $friendUserId, 'friend_id' => $actorId],
                ['updated_at' => $now, 'created_at' => $now]
            );

            FriendRequest::query()
                ->where('sender_user_id', $friendUserId)
                ->where('receiver_user_id', $actorId)
                ->update([
                    'status' => 'accepted',
                    'accepted_at' => $now,
                    'rejected_at' => null,
                    'updated_at' => $now,
                ]);

            FriendRequest::query()
                ->where('sender_user_id', $actorId)
                ->where('receiver_user_id', $friendUserId)
                ->delete();

            return FriendRequest::query()
                ->where('sender_user_id', $friendUserId)
                ->where('receiver_user_id', $actorId)
                ->firstOrFail();
        });
    }

    public function reject(User $actor, int $friendUserId): FriendRequest
    {
        $actorId = (int) $actor->id;
        $friendUserId = (int) $friendUserId;

        $incoming = FriendRequest::query()
            ->where('sender_user_id', $friendUserId)
            ->where('receiver_user_id', $actorId)
            ->firstOrFail();

        if ($incoming->status !== 'pending') {
            throw ValidationException::withMessages([
                'friendship' => [__('api.friendship.not_pending')],
            ]);
        }

        return DB::transaction(function () use ($incoming) {
            $now = now();

            $incoming->update([
                'status' => 'rejected',
                'rejected_at' => $now,
                'accepted_at' => null,
            ]);

            return $incoming->fresh();
        });
    }

    public function cancelOrDecline(User $actor, int $friendUserId): void
    {
        $actorId = (int) $actor->id;
        $friendUserId = (int) $friendUserId;

        $row = FriendRequest::query()
            ->where(function ($q) use ($actorId, $friendUserId) {
                $q->where('sender_user_id', $actorId)->where('receiver_user_id', $friendUserId);
            })
            ->orWhere(function ($q) use ($actorId, $friendUserId) {
                $q->where('sender_user_id', $friendUserId)->where('receiver_user_id', $actorId);
            })
            ->firstOrFail();

        if ($row->status !== 'pending') {
            throw ValidationException::withMessages([
                'friendship' => [__('api.friendship.not_pending')],
            ]);
        }

        $row->delete();
    }

    public function unfriend(User $actor, int $friendUserId): void
    {
        $actorId = (int) $actor->id;
        $friendUserId = (int) $friendUserId;

        $row = Friend::query()
            ->where('user_id', $actorId)
            ->where('friend_id', $friendUserId)
            ->firstOrFail();
        unset($row);

        DB::transaction(function () use ($actorId, $friendUserId) {
            Friend::query()
                ->where('user_id', $actorId)
                ->where('friend_id', $friendUserId)
                ->delete();

            Friend::query()
                ->where('user_id', $friendUserId)
                ->where('friend_id', $actorId)
                ->delete();
        });
    }

    public function listFriends(User $user, ?string $q = null): LengthAwarePaginator
    {
        $userId = (int) $user->id;

        $friendIdsQuery = Friend::query()
            ->select('friend_id')
            ->where('user_id', $userId);

        return User::query()
            ->whereIn('id', $friendIdsQuery)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('nick_name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function listIncomingRequests(User $user): LengthAwarePaginator
    {
        $userId = (int) $user->id;

        return FriendRequest::query()
            ->where('status', 'pending')
            ->where('receiver_user_id', $userId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(10);
    }

    public function listOutgoingRequests(User $user): LengthAwarePaginator
    {
        $userId = (int) $user->id;

        return FriendRequest::query()
            ->where('status', 'pending')
            ->where('sender_user_id', $userId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(10);
    }
}

