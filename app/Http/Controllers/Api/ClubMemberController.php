<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Club\InviteClubMembersRequest;
use App\Http\Resources\ClubMemberResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Club;
use App\Models\ClubMember;
use App\Repositories\Club\ClubRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClubMemberController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private ClubRepositoryInterface $clubRepository
    ) {}

    /**
     * Captain invites users to join club (they appear as pending until accept).
     */
    public function invite(InviteClubMembersRequest $request, Club $club): JsonResponse
    {
        $validated = $request->validated();

        $memberships = $this->clubRepository->inviteMembers(
            $club,
            $request->user(),
            $validated['user_ids'],
            $validated['role'],
        );

        $memberships = new \Illuminate\Database\Eloquent\Collection($memberships);

        return $this->success([
            'invites' => ClubMemberResource::collection(
                $memberships->loadMissing(['user', 'club'])
            ),
        ], 'club.invites_sent', 201);
    }

    /**
     * List pending club invitations for the authenticated user.
     */
    public function myInvites(Request $request): JsonResponse
    {
        $invites = $this->clubRepository->myPendingInvites($request->user());

        return $this->success([
            'invites' => ClubMemberResource::collection($invites),
        ]);
    }

    public function accept(Request $request, ClubMember $clubMember): JsonResponse
    {
        $membership = $this->clubRepository->acceptInvite($request->user(), $clubMember);

        return $this->success([
            'membership' => new ClubMemberResource($membership),
        ], 'club.invite_accepted');
    }

    public function destroy(Request $request, ClubMember $clubMember): JsonResponse
    {
        $this->clubRepository->rejectInvite($request->user(), $clubMember);

        return $this->success([], 'club.invite_rejected');
    }
}

