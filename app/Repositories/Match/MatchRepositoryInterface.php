<?php

namespace App\Repositories\Match;

use App\Models\GameMatch;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MatchRepositoryInterface
{
    public function currentForUser(User $user): ?GameMatch;

    public function recordResult(
        User $owner,
        GameMatch $match,
        array $data
    ): GameMatch;

    public function historyForStadium(User $owner): LengthAwarePaginator;

    public function createManual(User $owner, array $data): GameMatch;
}

