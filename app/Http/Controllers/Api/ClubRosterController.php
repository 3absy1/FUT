<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Club\ClubMemberSearchRequest;
use App\Http\Resources\ClubMemberResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Club;
use App\Repositories\Club\ClubRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ClubRosterController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private ClubRepositoryInterface $clubRepository
    ) {}

    /**
     * List/search club roster members (used to select teammates).
     * Requires the authenticated user to be an active member of this club.
     */
    public function members(ClubMemberSearchRequest $request, Club $club): JsonResponse
    {
        $members = $this->clubRepository->searchMembers(
            $club,
            $request->user(),
            $request->validated()['q'] ?? null
        );

        return $this->success([
            'members' => ClubMemberResource::collection($members),
        ]);
    }
}

