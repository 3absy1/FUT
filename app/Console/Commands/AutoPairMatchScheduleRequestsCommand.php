<?php

namespace App\Console\Commands;

use App\Repositories\MatchScheduleRequest\MatchScheduleRequestRepositoryInterface;
use Illuminate\Console\Command;

class AutoPairMatchScheduleRequestsCommand extends Command
{
    protected $signature = 'match-schedule-requests:auto-pair';

    protected $description = 'Auto-pair pending match schedule requests that share area and identical slot start time';

    public function handle(MatchScheduleRequestRepositoryInterface $repository): int
    {
        $paired = $repository->autoPairAllPendingMatchScheduleRequests();
        $this->info("Auto-paired {$paired} match schedule request(s).");

        return self::SUCCESS;
    }
}
