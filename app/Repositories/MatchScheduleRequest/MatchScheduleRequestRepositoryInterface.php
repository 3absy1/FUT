<?php

namespace App\Repositories\MatchScheduleRequest;

use App\Models\MatchScheduleRequest;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MatchScheduleRequestRepositoryInterface
{
    public function create(User $actor, array $data): MatchScheduleRequest;

    public function listForUser(User $actor): LengthAwarePaginator;

    public function findForUser(User $actor, MatchScheduleRequest $request): MatchScheduleRequest;

    public function recentByArea(User $actor, int $areaId): LengthAwarePaginator;

    public function nearbyPendingUnpairedByArea(User $actor, int $areaId): LengthAwarePaginator;

    public function join(
        User $actor,
        MatchScheduleRequest $request,
        array $data
    ): MatchScheduleRequest;

    public function acceptByStadiumOwner(
        User $owner,
        MatchScheduleRequest $request,
        array $data
    ): MatchScheduleRequest;

    public function listForStadiumOwner(User $owner, ?string $status = null): LengthAwarePaginator;

    /**
     * Pair pending unpaired requests that share the same area and an identical slot start time.
     * Returns how many merges were performed.
     */
    public function autoPairAllPendingMatchScheduleRequests(): int;
}
