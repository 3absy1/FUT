<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * Player leaderboard.
     *
     * Returns:
     * - top3: best 3 players
     * - me: authenticated user + rank
     * - leaderboard: paginated list (defaults to the page where "me" is)
     */
    public function players(Request $request): JsonResponse
    {
        /** @var User $me */
        $me = $request->user();

        $perPage = (int) ($request->query('per_page', 20));
        $perPage = max(5, min(100, $perPage));

        $baseQuery = User::query()
            ->where('is_stadium_owner', false)
            ->orderByDesc('exp')
            ->orderBy('id');

        $top3 = (clone $baseQuery)->limit(3)->get();

        $myRank = $this->rankForUser($me);
        $defaultPage = (int) ceil(max(1, $myRank) / $perPage);

        $page = (int) ($request->query('page', $defaultPage));
        $page = max(1, $page);

        $paginator = $baseQuery->paginate($perPage, ['*'], 'page', $page);

        return $this->success([
            'top3' => UserResource::collection($top3),
            'me' => [
                'rank' => $myRank,
                'user' => new UserResource($me),
            ],
            'leaderboard' => UserResource::collection($paginator),
            'meta' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'my_page' => $defaultPage,
            ],
        ]);
    }

    /**
     * Club leaderboard.
     *
     * Returns:
     * - top3: best 3 clubs
     * - my_club: authenticated user's active club + rank + page
     * - leaderboard: paginated list (defaults to the page where "my_club" is)
     */
    public function clubs(Request $request): JsonResponse
    {
        /** @var User $me */
        $me = $request->user();

        $perPage = (int) ($request->query('per_page', 20));
        $perPage = max(5, min(100, $perPage));

        $baseQuery = Club::query()
            ->orderByDesc('exp')
            ->orderBy('id');

        $top3 = (clone $baseQuery)->limit(3)->get();

        $membership = ClubMember::query()
            ->where('user_id', $me->id)
            ->where('is_active', true)
            ->with('club.area')
            ->latest()
            ->first();

        $myClub = $membership?->club;
        $myRank = $myClub ? $this->rankForClub($myClub) : null;
        $myPage = $myRank ? (int) ceil($myRank / $perPage) : 1;

        $page = (int) ($request->query('page', $myPage));
        $page = max(1, $page);

        $paginator = $baseQuery->with('area')->paginate($perPage, ['*'], 'page', $page);

        return $this->success([
            'top3' => ClubResource::collection($top3),
            'my_club' => $myClub ? [
                'rank' => $myRank,
                'page' => $myPage,
                'club' => new ClubResource($myClub),
            ] : null,
            'leaderboard' => ClubResource::collection($paginator),
            'meta' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'my_page' => $myPage,
            ],
        ]);
    }

    private function rankForUser(User $user): int
    {
        $higherCount = User::query()
            ->where('is_stadium_owner', false)
            ->where(function ($q) use ($user) {
                $q->where('exp', '>', (int) $user->exp)
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('exp', (int) $user->exp)
                            ->where('id', '<', (int) $user->id);
                    });
            })
            ->count();

        return $higherCount + 1;
    }

    private function rankForClub(Club $club): int
    {
        $higherCount = Club::query()
            ->where(function ($q) use ($club) {
                $q->where('exp', '>', (int) $club->exp)
                    ->orWhere(function ($q2) use ($club) {
                        $q2->where('exp', (int) $club->exp)
                            ->where('id', '<', (int) $club->id);
                    });
            })
            ->count();

        return $higherCount + 1;
    }
}

