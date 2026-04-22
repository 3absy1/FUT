<?php

namespace App\Repositories\Match;

use App\Models\Club;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\Pitch;
use App\Models\Division;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MatchRepository implements MatchRepositoryInterface
{
    public function currentForUser(User $user): ?GameMatch
    {
        return GameMatch::query()
            ->whereIn('status', ['ongoing', 'scheduled'])
            ->whereExists(function ($query) use ($user) {
                $query->selectRaw('1')
                    ->from('match_players')
                    ->whereColumn('match_players.match_id', 'matches.id')
                    ->where('match_players.user_id', $user->id)
                    ->where('match_players.is_playing', true);
            })
            // Prefer ongoing first, then nearest scheduled one.
            ->orderByRaw("CASE WHEN status = 'ongoing' THEN 0 ELSE 1 END")
            ->orderBy('scheduled_datetime')
            ->with(['clubA', 'clubB', 'stadium', 'pitch'])
            ->first();
    }

    public function historyForStadium(User $owner): LengthAwarePaginator
    {
        if (! $owner->is_stadium_owner || ! $owner->stadium_id) {
            throw ValidationException::withMessages([
                'user' => [__('api.match_result.not_stadium_owner')],
            ]);
        }

        return GameMatch::query()
            ->where('stadium_id', $owner->stadium_id)
            ->with(['clubA', 'clubB', 'stadium', 'pitch'])
            ->orderByDesc('scheduled_datetime')
            ->paginate(15);
    }

    public function createManual(User $owner, array $data): GameMatch
    {
        if (! $owner->is_stadium_owner || ! $owner->stadium_id) {
            throw ValidationException::withMessages([
                'user' => [__('api.match_result.not_stadium_owner')],
            ]);
        }

        $pitchOk = Pitch::query()
            ->whereKey((int) $data['pitch_id'])
            ->where('stadium_id', $owner->stadium_id)
            ->exists();

        if (! $pitchOk) {
            throw ValidationException::withMessages([
                'pitch_id' => [__('api.pitch.invalid_for_stadium')],
            ]);
        }

        if ((int) $data['club_a_id'] === (int) $data['club_b_id']) {
            throw ValidationException::withMessages([
                'club_b_id' => [__('api.match_manual.same_clubs')],
            ]);
        }

        $status = $data['status'] ?? 'scheduled';
        if (! in_array($status, ['scheduled', 'pending', 'ongoing'], true)) {
            throw ValidationException::withMessages([
                'status' => [__('api.match_manual.invalid_status')],
            ]);
        }

        return DB::transaction(function () use ($owner, $data, $status) {
            $match = GameMatch::create([
                'club_a_id' => (int) $data['club_a_id'],
                'club_b_id' => (int) $data['club_b_id'],
                'stadium_id' => $owner->stadium_id,
                'pitch_id' => (int) $data['pitch_id'],
                'scheduled_datetime' => $data['scheduled_datetime'],
                'status' => $status,
                'score_club_a' => (int) ($data['score_club_a'] ?? 0),
                'score_club_b' => (int) ($data['score_club_b'] ?? 0),
                'tournament_id' => null,
            ]);

            return $match->fresh(['clubA', 'clubB', 'stadium', 'pitch']);
        });
    }

    public function recordResult(
        User $owner,
        GameMatch $match,
        array $data
    ): GameMatch {
        if (! $owner->is_stadium_owner || ! $owner->stadium_id) {
            throw ValidationException::withMessages([
                'user' => [__('api.match_result.not_stadium_owner')],
            ]);
        }

        if ((int) $match->stadium_id !== (int) $owner->stadium_id) {
            throw ValidationException::withMessages([
                'match' => [__('api.match_result.not_for_this_stadium')],
            ]);
        }

        if ($match->status === 'completed') {
            throw ValidationException::withMessages([
                'match' => [__('api.match_result.already_completed')],
            ]);
        }

        $winner = $data['winner']; // club_a, club_b, draw

        return DB::transaction(function () use ($match, $winner) {
            $match->loadMissing(['clubA.area', 'clubB.area', 'stadium.area']);

            $clubAAreaId = $match->clubA?->area_id;
            $clubBAreaId = $match->clubB?->area_id;
            $stadiumAreaId = $match->stadium?->area_id;

            if (! $clubAAreaId || ! $clubBAreaId || $clubAAreaId !== $clubBAreaId) {
                throw ValidationException::withMessages([
                    'match' => [__('api.match_result.clubs_must_be_same_area')],
                ]);
            }

            if ($stadiumAreaId && $stadiumAreaId !== $clubAAreaId) {
                throw ValidationException::withMessages([
                    'match' => [__('api.match_result.stadium_area_mismatch')],
                ]);
            }

            $match->update([
                'score_club_a' => null,
                'score_club_b' => null,
                'result' => $winner,
                'status' => 'completed',
            ]);

            $players = MatchPlayer::query()
                ->where('match_id', $match->id)
                ->where('is_playing', true)
                ->get();

            $clubAUserIds = $players->where('club_id', $match->club_a_id)->pluck('user_id')->all();
            $clubBUserIds = $players->where('club_id', $match->club_b_id)->pluck('user_id')->all();

            if ($winner === 'club_a') {
                $this->applyWinLoss($clubAUserIds, $clubBUserIds, $match->club_a_id, $match->club_b_id);
            } elseif ($winner === 'club_b') {
                $this->applyWinLoss($clubBUserIds, $clubAUserIds, $match->club_b_id, $match->club_a_id);
            } else {
                $this->applyDraw($clubAUserIds, $clubBUserIds, $match->club_a_id, $match->club_b_id);
            }

            return $match->fresh(['clubA', 'clubB', 'stadium', 'pitch', 'matchPlayers.user']);
        });
    }

    private function applyWinLoss(
        array $winnerUserIds,
        array $loserUserIds,
        int $winnerClubId,
        int $loserClubId
    ): void {
        $this->applyDivisionProgressForUsers($winnerUserIds, true);
        $this->applyDivisionProgressForUsers($loserUserIds, false);

        $winnerClubExp = $this->applyWinExpForUsers($winnerUserIds);

        if ($winnerClubExp > 0) {
            Club::where('id', $winnerClubId)->increment('exp', $winnerClubExp);
        }
    }

    private function applyDraw(
        array $clubAUserIds,
        array $clubBUserIds,
        int $clubAId,
        int $clubBId
    ): void
    {
        $clubAExp = $this->applyDrawExpForUsers($clubAUserIds);
        $clubBExp = $this->applyDrawExpForUsers($clubBUserIds);

        if ($clubAExp > 0) {
            Club::where('id', $clubAId)->increment('exp', $clubAExp);
        }
        if ($clubBExp > 0) {
            Club::where('id', $clubBId)->increment('exp', $clubBExp);
        }
    }

    private function applyWinExpForUsers(array $userIds): int
    {
        if (! $userIds) {
            return 0;
        }

        $users = User::query()
            ->whereIn('id', $userIds)
            ->where('is_stadium_owner', false)
            ->with('division')
            ->get();

        $totalAwarded = 0;
        foreach ($users as $user) {
            $expWin = (int) ($user->division?->exp_win ?? 5);
            if ($expWin > 0) {
                $user->increment('exp', $expWin);
                $totalAwarded += $expWin;
            }
        }

        return $totalAwarded;
    }

    private function applyDrawExpForUsers(array $userIds): int
    {
        if (! $userIds) {
            return 0;
        }

        $users = User::query()
            ->whereIn('id', $userIds)
            ->where('is_stadium_owner', false)
            ->with('division')
            ->get();

        $totalAwarded = 0;
        foreach ($users as $user) {
            $drawExp = (int) ($user->division?->draw_exp ?? 2);
            if ($drawExp > 0) {
                $user->increment('exp', $drawExp);
                $totalAwarded += $drawExp;
            }
        }

        return $totalAwarded;
    }

    private function applyDivisionProgressForUsers(array $userIds, bool $isWin): void
    {
        if (! $userIds) {
            return;
        }

        $users = User::query()
            ->whereIn('id', $userIds)
            ->where('is_stadium_owner', false)
            ->with('division')
            ->get();

        foreach ($users as $user) {
            $division = $user->division;
            if (! $division) {
                continue;
            }

            $matchesCount = (int) ($division->matches_count ?? 0);
            $checkpoints = array_values(array_filter($division->checkpoints ?? [], fn ($v) => is_int($v) || ctype_digit((string) $v)));
            $checkpoints = array_map('intval', $checkpoints);

            $current = (int) ($user->division_current_match ?? 0);
            $lastCheckpoint = (int) ($user->division_last_checkpoint_match ?? 0);

            if ($isWin) {
                $current++;

                if (in_array($current, $checkpoints, true)) {
                    $lastCheckpoint = $current;
                }

                // Completed division -> move to harder one (10 -> 9 -> ... -> 1)
                if ($matchesCount > 0 && $current >= $matchesCount) {
                    $nextDivisionId = Division::query()
                        ->where('sort_order', ((int) $division->sort_order) - 1)
                        ->value('id');

                    if ($nextDivisionId) {
                        $user->update([
                            'division_id' => $nextDivisionId,
                            'division_current_match' => 0,
                            'division_last_checkpoint_match' => 0,
                        ]);
                        continue;
                    }
                }
            } else {
                // Loss: never go below last checkpoint; if between checkpoints, drop by 1.
                if ($current > $lastCheckpoint) {
                    $current = max($lastCheckpoint, $current - 1);
                }
            }

            $user->update([
                'division_current_match' => $current,
                'division_last_checkpoint_match' => $lastCheckpoint,
            ]);
        }
    }
}

