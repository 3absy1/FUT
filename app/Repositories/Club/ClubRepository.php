<?php

namespace App\Repositories\Club;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ClubRepository implements ClubRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return Club::with('area')
            ->latest()
            ->paginate(10);
    }

    public function findById(int $id): Club
    {
        return Club::with('area')->findOrFail($id);
    }

    public function create(User $owner, array $data): Club
    {
        $club = Club::create([
            'name' => $data['name'],
            'icon' => $data['icon'] ?? null,
            'max_players' => $data['max_players'] ?? 22,
            'rating' => $data['rating'] ?? 0,
            'exp' => 0,
            'area_id' => $data['area_id'] ?? null,
        ]);

        ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $owner->id,
            'role' => 'captain',
            'is_active' => true,
        ]);

        return $club->fresh();
    }

    public function update(User $actor, Club $club, array $data): Club
    {
        $this->assertIsCaptain($actor, $club);

        $club->update([
            'name' => $data['name'] ?? $club->name,
            'icon' => $data['icon'] ?? $club->icon,
            'max_players' => $data['max_players'] ?? $club->max_players,
            'rating' => $data['rating'] ?? $club->rating,
            'area_id' => $data['area_id'] ?? $club->area_id,
        ]);

        return $club->fresh();
    }

    public function delete(User $actor, Club $club): void
    {
        $this->assertIsCaptain($actor, $club);

        $club->delete();
    }

    public function searchMembers(Club $club, User $actor, ?string $q = null): LengthAwarePaginator
    {
        $this->assertIsActiveMember($actor, $club);

        return $club->activeMembers()
            ->with('user')
            ->when($q, function ($query) use ($q) {
                $query->whereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                        ->orWhere('nick_name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function inviteMembers(Club $club, User $actor, array $userIds): array
    {
        $this->assertIsCaptain($actor, $club);

        $userIds = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->reject(fn ($id) => $id === (int) $actor->id)
            ->values();

        if ($userIds->isEmpty()) {
            return [];
        }

        $existingMemberships = ClubMember::query()
            ->where('club_id', $club->id)
            ->whereIn('user_id', $userIds->all())
            ->pluck('user_id')
            ->all();

        $toInvite = $userIds->reject(fn ($id) => in_array($id, $existingMemberships, true))->values();

        $activeCount = $club->activeMembers()->count();
        $pendingCount = ClubMember::query()
            ->where('club_id', $club->id)
            ->where('is_active', false)
            ->count();

        $availableSlots = max(0, (int) $club->max_players - ($activeCount + $pendingCount));

        if ($toInvite->count() > $availableSlots) {
            throw ValidationException::withMessages([
                'user_ids' => [__('api.club.max_players_reached')],
            ]);
        }

        $created = [];

        foreach ($toInvite as $userId) {
            $created[] = ClubMember::create([
                'club_id' => $club->id,
                'user_id' => $userId,
                'role' => 'player',
                'is_active' => false,
            ]);
        }

        return $created;
    }

    public function myPendingInvites(User $user): LengthAwarePaginator
    {
        return ClubMember::query()
            ->where('user_id', $user->id)
            ->where('is_active', false)
            ->with('club')
            ->latest()
            ->paginate(10);
    }

    public function acceptInvite(User $user, ClubMember $membership): ClubMember
    {
        $this->assertInviteBelongsToUser($user, $membership);

        if ($membership->is_active) {
            return $membership;
        }

        $club = $membership->club;

        $activeCount = $club->activeMembers()->count();
        if ($activeCount >= $club->max_players) {
            throw ValidationException::withMessages([
                'membership' => [__('api.club.max_players_reached')],
            ]);
        }

        $membership->update([
            'is_active' => true,
        ]);

        return $membership->fresh(['club', 'user']);
    }

    public function rejectInvite(User $user, ClubMember $membership): void
    {
        $this->assertInviteBelongsToUser($user, $membership);

        if (! $membership->is_active) {
            $membership->delete();
            return;
        }

        throw ValidationException::withMessages([
            'membership' => [__('api.club.cannot_reject_active')],
        ]);
    }

    private function assertIsActiveMember(User $user, Club $club): void
    {
        $isMember = $club->members()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();

        if (! $isMember) {
            throw ValidationException::withMessages([
                'club_id' => [__('api.club.not_a_member')],
            ]);
        }
    }

    private function assertIsCaptain(User $user, Club $club): void
    {
        $isCaptain = $club->members()
            ->where('user_id', $user->id)
            ->where('role', 'captain')
            ->where('is_active', true)
            ->exists();

        if (! $isCaptain) {
            throw ValidationException::withMessages([
                'club_id' => [__('api.club.not_captain')],
            ]);
        }
    }

    private function assertInviteBelongsToUser(User $user, ClubMember $membership): void
    {
        if ((int) $membership->user_id !== (int) $user->id) {
            throw ValidationException::withMessages([
                'membership' => [__('api.club.invite_not_for_user')],
            ]);
        }
    }
}

