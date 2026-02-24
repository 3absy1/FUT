<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Friendship\SendFriendRequestRequest;
use App\Http\Requests\Friendship\UserSearchRequest;
use App\Http\Resources\FriendshipRequestResource;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Friendship;
use App\Repositories\Friendship\FriendshipRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private FriendshipRepositoryInterface $friendshipRepository
    ) {}

    /**
     * Search all users (to add friend).
     */
    public function searchUsers(UserSearchRequest $request): JsonResponse
    {
        $users = $this->friendshipRepository->searchUsers(
            $request->validated()['q'],
            $request->user()->id
        );

        return $this->success([
            'users' => UserResource::collection($users),
        ]);
    }

    /**
     * List accepted friends (used to select teammates).
     */
    public function index(Request $request): JsonResponse
    {
        $friends = $this->friendshipRepository->listFriends(
            $request->user(),
            $request->query('q')
        );

        return $this->success([
            'friends' => UserResource::collection($friends),
        ]);
    }

    public function sendRequest(SendFriendRequestRequest $request): JsonResponse
    {
        $friendship = $this->friendshipRepository->sendRequest(
            $request->user(),
            (int) $request->validated()['friend_user_id']
        );

        return $this->success([
            'friend_request' => new FriendshipRequestResource($friendship),
        ], 'friendship.request_sent', 201);
    }

    public function incoming(Request $request): JsonResponse
    {
        $incoming = $this->friendshipRepository->listIncomingRequests($request->user());

        return $this->success([
            'incoming_requests' => FriendshipRequestResource::collection($incoming),
        ]);
    }

    public function outgoing(Request $request): JsonResponse
    {
        $outgoing = $this->friendshipRepository->listOutgoingRequests($request->user());

        return $this->success([
            'outgoing_requests' => FriendshipRequestResource::collection($outgoing),
        ]);
    }

    public function accept(Request $request, Friendship $friendship): JsonResponse
    {
        $friendship = $this->friendshipRepository->accept($request->user(), $friendship);

        return $this->success([
            'friend_request' => new FriendshipRequestResource($friendship),
        ], 'friendship.request_accepted');
    }

    /**
     * Cancel (if outgoing), decline (if incoming), or unfriend (if accepted).
     */
    public function destroy(Request $request, Friendship $friendship): JsonResponse
    {
        $this->friendshipRepository->delete($request->user(), $friendship);

        return $this->success([], 'friendship.deleted');
    }
}

