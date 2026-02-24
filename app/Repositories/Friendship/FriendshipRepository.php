<?php

namespace App\Repositories\Friendship;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public function sendRequest(User $requester, int $friendUserId): Friendship
    {
        if ($requester->id === $friendUserId) {
            throw ValidationException::withMessages([
                'friend_user_id' => [__('api.friendship.cannot_friend_self')],
            ]);
        }

        $friend = User::findOrFail($friendUserId);

        [$userId, $friendId] = $this->canonicalPair($requester->id, $friend->id);

        $existing = Friendship::where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->first();

        if ($existing) {
            if ($existing->status === 'pending') {
                throw ValidationException::withMessages([
                    'friend_user_id' => [__('api.friendship.request_already_pending')],
                ]);
            }
            if ($existing->status === 'accepted') {
                throw ValidationException::withMessages([
                    'friend_user_id' => [__('api.friendship.already_friends')],
                ]);
            }
            if ($existing->status === 'blocked') {
                throw ValidationException::withMessages([
                    'friend_user_id' => [__('api.friendship.cannot_request')],
                ]);
            }
        }

        return Friendship::create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'requested_by_user_id' => $requester->id,
            'status' => 'pending',
        ]);
    }

    public function accept(User $actor, Friendship $friendship): Friendship
    {
        if ($friendship->status !== 'pending') {
            throw ValidationException::withMessages([
                'friendship' => [__('api.friendship.not_pending')],
            ]);
        }

        if ($friendship->requested_by_user_id === $actor->id) {
            throw ValidationException::withMessages([
                'friendship' => [__('api.friendship.cannot_accept_own_request')],
            ]);
        }

        if (! $this->isParticipant($actor->id, $friendship)) {
            throw ValidationException::withMessages([
                'friendship' => [__('api.friendship.not_allowed')],
            ]);
        }

        $friendship->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'rejected_at' => null,
        ]);

        return $friendship->fresh();
    }

    public function delete(User $actor, Friendship $friendship): void
    {
        if (! $this->isParticipant($actor->id, $friendship)) {
            throw ValidationException::withMessages([
                'friendship' => [__('api.friendship.not_allowed')],
            ]);
        }

        $friendship->delete();
    }

    public function listFriends(User $user, ?string $q = null): LengthAwarePaginator
    {
        $userId = $user->id;

        $friendIdsQuery = Friendship::query()
            ->selectRaw("
                CASE
                    WHEN user_id = ? THEN friend_id
                    ELSE user_id
                END as friend_user_id
            ", [$userId])
            ->where('status', 'accepted')
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhere('friend_id', $userId);
            });

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
        $userId = $user->id;

        return Friendship::query()
            ->where('status', 'pending')
            ->where('requested_by_user_id', '!=', $userId)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhere('friend_id', $userId);
            })
            ->with(['requestedBy', 'user', 'friend'])
            ->latest()
            ->paginate(10);
    }

    public function listOutgoingRequests(User $user): LengthAwarePaginator
    {
        $userId = $user->id;

        return Friendship::query()
            ->where('status', 'pending')
            ->where('requested_by_user_id', $userId)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhere('friend_id', $userId);
            })
            ->with(['requestedBy', 'user', 'friend'])
            ->latest()
            ->paginate(10);
    }

    private function canonicalPair(int $a, int $b): array
    {
        return $a < $b ? [$a, $b] : [$b, $a];
    }

    private function isParticipant(int $userId, Friendship $friendship): bool
    {
        return $friendship->user_id === $userId || $friendship->friend_id === $userId;
    }
}

