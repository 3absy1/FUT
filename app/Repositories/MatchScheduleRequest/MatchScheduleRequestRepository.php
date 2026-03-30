<?php

namespace App\Repositories\MatchScheduleRequest;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Friend;
use App\Models\GameMatch;
use App\Models\MatchScheduleRequest;
use App\Models\MatchScheduleRequestPlayer;
use App\Models\MatchScheduleRequestSlot;
use App\Models\MatchPlayer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MatchScheduleRequestRepository implements MatchScheduleRequestRepositoryInterface
{
    public function create(User $actor, array $data): MatchScheduleRequest
    {
        $club = Club::findOrFail((int) $data['club_id']);

        $captainIsMember = $club->members()
            ->where('user_id', $actor->id)
            ->where('is_active', true)
            ->exists();

        if (! $captainIsMember) {
            throw ValidationException::withMessages([
                'club_id' => [__('api.club.not_a_member')],
            ]);
        }

        $teamSource = $data['team_source'] ?? 'club';
        if (! in_array($teamSource, ['club', 'friends'], true)) {
            throw ValidationException::withMessages([
                'team_source' => [__('api.match_schedule_request.invalid_team_source')],
            ]);
        }

        $teammateIds = collect($data['player_user_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $teammateIds = $teammateIds->reject(fn ($id) => $id === (int) $actor->id)->values();

        if ($teammateIds->count() > 4) {
            throw ValidationException::withMessages([
                'player_user_ids' => [__('api.match_schedule_request.max_players')],
            ]);
        }

        $slots = collect($data['schedule_slots'] ?? [])->values();
        if ($slots->isEmpty()) {
            throw ValidationException::withMessages([
                'schedule_slots' => [__('api.match_schedule_request.slots_required')],
            ]);
        }

        $normalizedSlots = $slots->map(function ($slot) {
            return [
                'start_datetime' => $slot['start_datetime'] ?? null,
                'end_datetime' => $slot['end_datetime'] ?? null,
            ];
        })->values();

        // Validate team membership depending on source
        if ($teamSource === 'club' && $teammateIds->isNotEmpty()) {
            $count = ClubMember::query()
                ->where('club_id', $club->id)
                ->where('is_active', true)
                ->whereIn('user_id', $teammateIds->all())
                ->count();

            if ($count !== $teammateIds->count()) {
                throw ValidationException::withMessages([
                    'player_user_ids' => [__('api.match_schedule_request.players_not_in_club')],
                ]);
            }
        }

        if ($teamSource === 'friends' && $teammateIds->isNotEmpty()) {
            $friendIds = $this->acceptedFriendIds($actor->id);

            $notFriends = $teammateIds->diff($friendIds);
            if ($notFriends->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'player_user_ids' => [__('api.match_schedule_request.players_not_friends')],
                ]);
            }
        }

        $areaId = $data['area_id'] ?? null;
        if (! $areaId) {
            $areaId = $club->area_id ?: $actor->area_id;
        }
        if (! $areaId) {
            throw ValidationException::withMessages([
                'area_id' => [__('api.match_schedule_request.area_required')],
            ]);
        }

        return DB::transaction(function () use ($actor, $data, $club, $teamSource, $teammateIds, $normalizedSlots) {
            $firstStart = $normalizedSlots->first()['start_datetime'] ?? null;

            $request = MatchScheduleRequest::create([
                'requested_by_user_id' => $actor->id,
                'club_id' => $club->id,
                'area_id' => (int) ($data['area_id'] ?? ($club->area_id ?: $actor->area_id)),
                'stadium_id' => null,
                'requested_datetime' => $firstStart,
                'team_source' => $teamSource,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'approved_at' => null,
            ]);

            // Players (captain + teammates)
            MatchScheduleRequestPlayer::create([
                'match_schedule_request_id' => $request->id,
                'user_id' => $actor->id,
                'team' => 'A',
                'role' => 'captain',
                'source' => 'self',
            ]);

            foreach ($teammateIds as $uid) {
                MatchScheduleRequestPlayer::create([
                    'match_schedule_request_id' => $request->id,
                    'user_id' => $uid,
                    'team' => 'A',
                    'role' => 'player',
                    'source' => $teamSource,
                ]);
            }

            foreach ($normalizedSlots as $slot) {
                MatchScheduleRequestSlot::create([
                    'match_schedule_request_id' => $request->id,
                    'start_datetime' => $slot['start_datetime'],
                    'end_datetime' => $slot['end_datetime'],
                ]);
            }

            return $request->load([
                'club',
                'opponentClub',
                'area',
                'stadium',
                'requestedBy',
                'players.user',
                'slots',
                'matchedSlot',
            ]);
        });
    }

    public function listForUser(User $actor): LengthAwarePaginator
    {
        return MatchScheduleRequest::query()
            ->where('requested_by_user_id', $actor->id)
            ->with(['club', 'opponentClub', 'area', 'stadium', 'players.user', 'slots', 'matchedSlot'])
            ->latest()
            ->paginate(10);
    }

    public function findForUser(User $actor, MatchScheduleRequest $request): MatchScheduleRequest
    {
        if ((int) $request->requested_by_user_id !== (int) $actor->id) {
            throw ValidationException::withMessages([
                'match_schedule_request' => [__('api.match_schedule_request.not_allowed')],
            ]);
        }

        return $request->load(['club', 'opponentClub', 'area', 'stadium', 'requestedBy', 'players.user', 'slots', 'matchedSlot']);
    }

    public function recentByArea(User $actor, int $areaId): LengthAwarePaginator
    {
        return MatchScheduleRequest::query()
            ->where('area_id', $areaId)
            ->whereNull('stadium_id') // not yet taken by a stadium
            ->where('status', 'pending')
            ->with(['club', 'opponentClub', 'area', 'requestedBy', 'players.user', 'slots', 'matchedSlot'])
            ->latest()
            ->paginate(10);
    }

    public function nearbyPendingUnpairedByArea(User $actor, int $areaId): LengthAwarePaginator
    {
        return MatchScheduleRequest::query()
            ->where('area_id', $areaId)
            ->where('status', 'pending')
            ->whereNull('opponent_club_id')
            ->whereNull('matched_slot_id')
            ->whereNull('stadium_id')
            ->whereNull('match_id')
            ->where('requested_by_user_id', '!=', $actor->id)
            ->with(['club', 'area', 'requestedBy', 'players.user', 'slots'])
            ->latest()
            ->paginate(10);
    }

    public function join(
        User $actor,
        MatchScheduleRequest $request,
        array $data
    ): MatchScheduleRequest {
        if ($request->requested_by_user_id === $actor->id) {
            throw ValidationException::withMessages([
                'match_schedule_request' => [__('api.match_schedule_request.cannot_join_own')],
            ]);
        }

        if ($request->status !== 'pending' || $request->opponent_club_id) {
            throw ValidationException::withMessages([
                'match_schedule_request' => [__('api.match_schedule_request.already_has_opponent')],
            ]);
        }

        $opponentClub = Club::findOrFail((int) ($data['opponent_club_id'] ?? 0));

        $isMember = $opponentClub->members()
            ->where('user_id', $actor->id)
            ->where('is_active', true)
            ->exists();

        if (! $isMember) {
            throw ValidationException::withMessages([
                'opponent_club_id' => [__('api.club.not_a_member')],
            ]);
        }

        $teamSource = $data['team_source'] ?? 'club';
        if (! in_array($teamSource, ['club', 'friends'], true)) {
            throw ValidationException::withMessages([
                'team_source' => [__('api.match_schedule_request.invalid_team_source')],
            ]);
        }

        $teammateIds = collect($data['player_user_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $teammateIds = $teammateIds->reject(fn ($id) => $id === (int) $actor->id)->values();

        if ($teammateIds->count() > 4) {
            throw ValidationException::withMessages([
                'player_user_ids' => [__('api.match_schedule_request.max_players')],
            ]);
        }

        if ($teamSource === 'club' && $teammateIds->isNotEmpty()) {
            $count = ClubMember::query()
                ->where('club_id', $opponentClub->id)
                ->where('is_active', true)
                ->whereIn('user_id', $teammateIds->all())
                ->count();

            if ($count !== $teammateIds->count()) {
                throw ValidationException::withMessages([
                    'player_user_ids' => [__('api.match_schedule_request.players_not_in_club')],
                ]);
            }
        }

        if ($teamSource === 'friends' && $teammateIds->isNotEmpty()) {
            $friendIds = $this->acceptedFriendIds($actor->id);

            $notFriends = $teammateIds->diff($friendIds);
            if ($notFriends->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'player_user_ids' => [__('api.match_schedule_request.players_not_friends')],
                ]);
            }
        }

        $slotId = (int) ($data['slot_id'] ?? 0);
        $slot = MatchScheduleRequestSlot::query()
            ->where('match_schedule_request_id', $request->id)
            ->where('id', $slotId)
            ->first();

        if (! $slot) {
            throw ValidationException::withMessages([
                'slot_id' => [__('api.match_schedule_request.slot_not_found')],
            ]);
        }

        return DB::transaction(function () use ($actor, $request, $opponentClub, $teamSource, $teammateIds, $slot) {
            $request->update([
                'opponent_club_id' => $opponentClub->id,
                'opponent_joined_by_user_id' => $actor->id,
                'matched_slot_id' => $slot->id,
            ]);

            MatchScheduleRequestPlayer::create([
                'match_schedule_request_id' => $request->id,
                'user_id' => $actor->id,
                'team' => 'B',
                'role' => 'captain',
                'source' => 'self',
            ]);

            foreach ($teammateIds as $uid) {
                MatchScheduleRequestPlayer::create([
                    'match_schedule_request_id' => $request->id,
                    'user_id' => $uid,
                    'team' => 'B',
                    'role' => 'player',
                    'source' => $teamSource,
                ]);
            }

            return $request->load([
                'club',
                'opponentClub',
                'area',
                'stadium',
                'requestedBy',
                'players.user',
                'slots',
                'matchedSlot',
            ]);
        });
    }

    public function acceptByStadiumOwner(
        User $owner,
        MatchScheduleRequest $request
    ): MatchScheduleRequest {
        if (! $owner->is_stadium_owner || ! $owner->stadium_id) {
            throw ValidationException::withMessages([
                'user' => [__('api.match_schedule_request.not_stadium_owner')],
            ]);
        }

        if ($request->status !== 'pending' || $request->stadium_id || ! $request->opponent_club_id || ! $request->matched_slot_id) {
            throw ValidationException::withMessages([
                'match_schedule_request' => [__('api.match_schedule_request.cannot_accept_by_stadium')],
            ]);
        }

        $slot = $request->matchedSlot ?? $request->slots()->where('id', $request->matched_slot_id)->first();

        if (! $slot) {
            throw ValidationException::withMessages([
                'match_schedule_request' => [__('api.match_schedule_request.slot_not_found')],
            ]);
        }

        return DB::transaction(function () use ($owner, $request, $slot) {
            $match = GameMatch::create([
                'club_a_id' => $request->club_id,
                'club_b_id' => $request->opponent_club_id,
                'stadium_id' => $owner->stadium_id,
                'scheduled_datetime' => $slot->start_datetime,
                'status' => 'scheduled',
                'score_club_a' => 0,
                'score_club_b' => 0,
                'tournament_id' => null,
            ]);

            // Copy players into match_players
            $players = $request->players()->get();

            foreach ($players as $player) {
                $clubId = $player->team === 'B'
                    ? $request->opponent_club_id
                    : $request->club_id;

                MatchPlayer::create([
                    'match_id' => $match->id,
                    'user_id' => $player->user_id,
                    'club_id' => $clubId,
                    'is_playing' => true,
                ]);
            }

            $request->update([
                'stadium_id' => $owner->stadium_id,
                'match_id' => $match->id,
                'status' => 'scheduled',
            ]);

            return $request->load([
                'club',
                'opponentClub',
                'area',
                'stadium',
                'requestedBy',
                'players.user',
                'slots',
                'matchedSlot',
            ]);
        });
    }

    private function acceptedFriendIds(int $userId)
    {
        return Friend::query()
            ->where('user_id', $userId)
            ->pluck('friend_id');
    }
}

