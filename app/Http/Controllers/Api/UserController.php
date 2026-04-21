<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ClubMember;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private UserRepositoryInterface $authRepository
    ) {}

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $stats = $this->buildSeasonStats($user->id, $user->position);

        return $this->success([
            'user' => new UserResource($user),
            'season_stats' => $stats,
        ]);
    }

    public function myClub(Request $request): JsonResponse
    {
        $membership = ClubMember::query()
            ->where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->with('club.area')
            ->latest()
            ->first();

        return $this->success([
            'club' => $membership?->club ? new ClubResource($membership->club) : null,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::query()
            ->whereKey($id)
            ->where('is_stadium_owner', false)
            ->with('division')
            ->firstOrFail();

        $stats = $this->buildSeasonStats($user->id, $user->position);

        return $this->success([
            'user' => new UserResource($user),
            'season_stats' => $stats,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->authRepository->updateProfile(
            $request->user(),
            $request->validated()
        );

        return $this->success([
            'user' => new UserResource($user),
        ], 'auth.profile_updated');
    }

    public function matchHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 50));

        $matches = GameMatch::query()
            ->with([
                'clubA:id,name',
                'clubB:id,name',
                'matchPlayers' => fn ($query) => $query
                    ->select('id', 'match_id', 'user_id', 'club_id')
                    ->where('user_id', $user->id),
            ])
            ->whereNotNull('result')
            ->whereHas('matchPlayers', fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('scheduled_datetime')
            ->orderByDesc('id')
            ->paginate($perPage);

        $history = $matches->getCollection()->map(function (GameMatch $match) {
            $playerRow = $match->matchPlayers->first();
            $playerClubId = $playerRow?->club_id;

            $isClubA = $playerClubId === $match->club_a_id;
            $againstClub = $isClubA ? $match->clubB : $match->clubA;
            $myScore = $isClubA ? $match->score_club_a : $match->score_club_b;
            $againstScore = $isClubA ? $match->score_club_b : $match->score_club_a;

            $isWin = ($isClubA && $match->result === 'club_a')
                || (! $isClubA && $match->result === 'club_b');
            $isDraw = $match->result === 'draw';

            return [
                'match_id' => $match->id,
                'scheduled_datetime' => $match->scheduled_datetime?->toIso8601String(),
                'status' => $match->status,
                'result' => $isDraw ? 'draw' : ($isWin ? 'won' : 'loss'),
                'against_club' => [
                    'id' => $againstClub?->id,
                    'name' => $againstClub?->name,
                ],
                'score' => [
                    'my_team' => (int) ($myScore ?? 0),
                    'against' => (int) ($againstScore ?? 0),
                    'display' => sprintf('%d - %d', (int) ($myScore ?? 0), (int) ($againstScore ?? 0)),
                ],
            ];
        })->values();

        $paginatedHistory = new LengthAwarePaginator(
            $history,
            $matches->total(),
            $matches->perPage(),
            $matches->currentPage(),
            ['path' => $matches->path(), 'pageName' => $matches->getPageName()]
        );

        return $this->success([
            'matches' => $paginatedHistory->items(),
            'pagination' => [
                'current_page' => $paginatedHistory->currentPage(),
                'last_page' => $paginatedHistory->lastPage(),
                'per_page' => $paginatedHistory->perPage(),
                'total' => $paginatedHistory->total(),
                'from' => $paginatedHistory->firstItem(),
                'to' => $paginatedHistory->lastItem(),
            ],
        ]);
    }

    private function buildSeasonStats(int $userId, ?string $position): array
    {
        $matchesQuery = MatchPlayer::query()
            ->join('matches', 'matches.id', '=', 'match_players.match_id')
            ->where('match_players.user_id', $userId)
            ->whereNotNull('matches.result');

        $totalMatches = (clone $matchesQuery)->count();

        $wins = (clone $matchesQuery)
            ->where(function ($query) {
                $query
                    ->where(function ($subQuery) {
                        $subQuery
                            ->where('matches.result', 'club_a')
                            ->whereColumn('matches.club_a_id', 'match_players.club_id');
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery
                            ->where('matches.result', 'club_b')
                            ->whereColumn('matches.club_b_id', 'match_players.club_id');
                    });
            })
            ->count();

        $winRate = $totalMatches > 0 ? round(($wins / $totalMatches) * 100, 2) : 0.0;

        $stats = [
            'total_matches' => $totalMatches,
            'wins' => $wins,
            'win_rate' => $winRate,
        ];

        if ($position === 'goal_keeper') {
            $cleanSheets = (clone $matchesQuery)
                ->where(function ($query) {
                    $query
                        ->where(function ($subQuery) {
                            $subQuery
                                ->whereColumn('matches.club_a_id', 'match_players.club_id')
                                ->where('matches.score_club_b', 0);
                        })
                        ->orWhere(function ($subQuery) {
                            $subQuery
                                ->whereColumn('matches.club_b_id', 'match_players.club_id')
                                ->where('matches.score_club_a', 0);
                        });
                })
                ->count();

            $stats['clean_sheets'] = $cleanSheets;
        }

        return $stats;
    }
}
