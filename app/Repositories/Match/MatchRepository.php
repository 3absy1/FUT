<?php

namespace App\Repositories\Match;

use App\Models\Club;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MatchRepository implements MatchRepositoryInterface
{
    private const USER_WIN_EXP = 20;
    private const USER_LOSE_EXP = 5;
    private const USER_DRAW_EXP = 10;

    private const CLUB_WIN_EXP = 50;
    private const CLUB_LOSE_EXP = 10;
    private const CLUB_DRAW_EXP = 20;

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
            ->with(['clubA', 'clubB', 'stadium'])
            ->first();
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
        $scoreA = (int) $data['score_club_a'];
        $scoreB = (int) $data['score_club_b'];

        if ($winner === 'club_a' && $scoreA <= $scoreB) {
            throw ValidationException::withMessages([
                'winner' => [__('api.match_result.winner_score_mismatch')],
            ]);
        }

        if ($winner === 'club_b' && $scoreB <= $scoreA) {
            throw ValidationException::withMessages([
                'winner' => [__('api.match_result.winner_score_mismatch')],
            ]);
        }

        if ($winner === 'draw' && $scoreA !== $scoreB) {
            throw ValidationException::withMessages([
                'winner' => [__('api.match_result.draw_score_mismatch')],
            ]);
        }

        return DB::transaction(function () use ($match, $winner, $scoreA, $scoreB) {
            $match->update([
                'score_club_a' => $scoreA,
                'score_club_b' => $scoreB,
                'status' => 'completed',
            ]);

            $players = MatchPlayer::query()
                ->where('match_id', $match->id)
                ->where('is_playing', true)
                ->get();

            $clubAUserIds = $players->where('club_id', $match->club_a_id)->pluck('user_id')->all();
            $clubBUserIds = $players->where('club_id', $match->club_b_id)->pluck('user_id')->all();

            if ($winner === 'club_a') {
                $this->applyExp($clubAUserIds, $clubBUserIds, $match->club_a_id, $match->club_b_id, 'A');
            } elseif ($winner === 'club_b') {
                $this->applyExp($clubBUserIds, $clubAUserIds, $match->club_b_id, $match->club_a_id, 'B');
            } else {
                $this->applyDrawExp($clubAUserIds, $clubBUserIds, $match->club_a_id, $match->club_b_id);
            }

            return $match->fresh(['clubA', 'clubB', 'stadium', 'matchPlayers.user']);
        });
    }

    private function applyExp(
        array $winnerUserIds,
        array $loserUserIds,
        int $winnerClubId,
        int $loserClubId,
        string $winnerSide
    ): void {
        if ($winnerUserIds) {
            User::whereIn('id', $winnerUserIds)->increment('exp', self::USER_WIN_EXP);
        }
        if ($loserUserIds) {
            User::whereIn('id', $loserUserIds)->increment('exp', self::USER_LOSE_EXP);
        }

        Club::where('id', $winnerClubId)->increment('exp', self::CLUB_WIN_EXP);
        Club::where('id', $loserClubId)->increment('exp', self::CLUB_LOSE_EXP);
    }

    private function applyDrawExp(
        array $clubAUserIds,
        array $clubBUserIds,
        int $clubAId,
        int $clubBId
    ): void {
        $allUserIds = array_merge($clubAUserIds, $clubBUserIds);
        if ($allUserIds) {
            User::whereIn('id', $allUserIds)->increment('exp', self::USER_DRAW_EXP);
        }

        Club::whereIn('id', [$clubAId, $clubBId])->increment('exp', self::CLUB_DRAW_EXP);
    }
}

