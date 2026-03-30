<?php

namespace App\Repositories\Match;

use App\Models\GameMatch;
use App\Models\User;

interface MatchRepositoryInterface
{
    public function currentForUser(User $user): ?GameMatch;

    public function recordResult(
        User $owner,
        GameMatch $match,
        array $data
    ): GameMatch;
}

