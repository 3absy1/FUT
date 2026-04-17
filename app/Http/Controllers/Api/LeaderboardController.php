<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
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
}

