<?php

namespace App\Repositories\Club;

use App\Models\Club;
use App\Models\User;
use App\Models\ClubMember;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClubRepositoryInterface
{
    public function getAll(): LengthAwarePaginator;

    public function findById(int $id): Club;

    public function create(User $owner, array $data): Club;

    public function update(User $actor, Club $club, array $data): Club;

    public function delete(User $actor, Club $club): void;

    public function searchMembers(Club $club, User $actor, ?string $q = null): LengthAwarePaginator;

    public function inviteMembers(Club $club, User $actor, array $userIds, string $role = 'player'): array;
    
    public function myPendingInvites(User $user): LengthAwarePaginator;

    public function acceptInvite(User $user, ClubMember $membership): ClubMember;

    public function rejectInvite(User $user, ClubMember $membership): void;
}

