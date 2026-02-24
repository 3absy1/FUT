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

    public function join(
        User $actor,
        MatchScheduleRequest $request,
        array $data
    ): MatchScheduleRequest;

    public function acceptByStadiumOwner(
        User $owner,
        MatchScheduleRequest $request
    ): MatchScheduleRequest;
}

