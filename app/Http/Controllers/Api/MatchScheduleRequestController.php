<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatchScheduleRequest\JoinMatchScheduleRequestRequest;
use App\Http\Requests\MatchScheduleRequest\RecentMatchRequestsRequest;
use App\Http\Requests\MatchScheduleRequest\StoreMatchScheduleRequestRequest;
use App\Http\Requests\MatchScheduleRequest\StadiumAcceptMatchRequest;
use App\Http\Resources\MatchScheduleRequestResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\MatchScheduleRequest;
use App\Repositories\MatchScheduleRequest\MatchScheduleRequestRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchScheduleRequestController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private MatchScheduleRequestRepositoryInterface $matchScheduleRequestRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $requests = $this->matchScheduleRequestRepository->listForUser($request->user());

        return $this->success([
            'requests' => MatchScheduleRequestResource::collection($requests),
        ]);
    }

    public function show(Request $request, MatchScheduleRequest $matchScheduleRequest): JsonResponse
    {
        $requestModel = $this->matchScheduleRequestRepository->findForUser(
            $request->user(),
            $matchScheduleRequest
        );

        return $this->success([
            'request' => new MatchScheduleRequestResource($requestModel),
        ]);
    }

    public function store(StoreMatchScheduleRequestRequest $request): JsonResponse
    {
        $created = $this->matchScheduleRequestRepository->create(
            $request->user(),
            $request->validated()
        );

        return $this->success([
            'request' => new MatchScheduleRequestResource($created),
            'payment' => [
                'required' => true,
                'status' => 'unpaid',
            ],
        ], 'match_schedule_request.created', 201);
    }

    /**
     * Home page: recent open match requests by area.
     * Uses user.area_id by default, or accepts ?area_id=
     */
    public function recent(RecentMatchRequestsRequest $request): JsonResponse
    {
        $areaId = $request->validated()['area_id'] ?? $request->user()->area_id;

        if (! $areaId) {
            return $this->error(
                'match_schedule_request.area_required',
                'error',
                'AREA_REQUIRED',
                [__('api.match_schedule_request.area_required')],
                422
            );
        }

        $requests = $this->matchScheduleRequestRepository->recentByArea($request->user(), (int) $areaId);

        return $this->success([
            'requests' => MatchScheduleRequestResource::collection($requests),
        ]);
    }

    /**
     * Opponent team joins an existing request from home page.
     */
    public function join(
        JoinMatchScheduleRequestRequest $request,
        MatchScheduleRequest $matchScheduleRequest
    ): JsonResponse {
        $joined = $this->matchScheduleRequestRepository->join(
            $request->user(),
            $matchScheduleRequest,
            $request->validated()
        );

        return $this->success([
            'request' => new MatchScheduleRequestResource($joined),
        ], 'match_schedule_request.joined');
    }

    /**
     * Stadium owner accepts to host this match (called by WhatsApp bot or app).
     */
    public function acceptByStadium(
        StadiumAcceptMatchRequest $request,
        MatchScheduleRequest $matchScheduleRequest
    ): JsonResponse {
        $updated = $this->matchScheduleRequestRepository->acceptByStadiumOwner(
            $request->user(),
            $matchScheduleRequest
        );

        return $this->success([
            'request' => new MatchScheduleRequestResource($updated),
        ], 'match_schedule_request.accepted_by_stadium');
    }
}

