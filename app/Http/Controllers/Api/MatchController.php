<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Match\RecordMatchResultRequest;
use App\Http\Requests\Match\StoreManualMatchRequest;
use App\Http\Resources\MatchResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\GameMatch;
use App\Repositories\Match\MatchRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private MatchRepositoryInterface $matchRepository
    ) {}

    /**
     * Get current match for authenticated user (nullable).
     */
    public function current(Request $request): JsonResponse
    {
        $match = $this->matchRepository->currentForUser($request->user());

        return $this->success([
            'match' => $match ? new MatchResource($match) : null,
        ]);
    }

    /**
     * Stadium owner records match result (winner + scores).
     */
    public function recordResult(
        RecordMatchResultRequest $request,
        GameMatch $match
    ): JsonResponse {
        $updated = $this->matchRepository->recordResult(
            $request->user(),
            $match,
            $request->validated()
        );

        return $this->success([
            'match' => new MatchResource($updated),
        ], 'match_result.recorded');
    }

    /**
     * Paginated match history for the authenticated stadium.
     */
    public function stadiumHistory(Request $request): JsonResponse
    {
        $matches = $this->matchRepository->historyForStadium($request->user());

        return $this->success([
            'matches' => MatchResource::collection($matches),
        ]);
    }

    /**
     * Create a match manually (not from a schedule request); appears in stadium history.
     */
    public function storeManual(StoreManualMatchRequest $request): JsonResponse
    {
        $match = $this->matchRepository->createManual(
            $request->user(),
            $request->validated()
        );

        return $this->success([
            'match' => new MatchResource($match),
        ], 'match_manual.created', 201);
    }
}

