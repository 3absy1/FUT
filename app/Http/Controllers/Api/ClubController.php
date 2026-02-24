<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Club\StoreClubRequest;
use App\Http\Requests\Club\UpdateClubRequest;
use App\Http\Resources\ClubResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Club;
use App\Repositories\Club\ClubRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private ClubRepositoryInterface $clubRepository
    ) {}

    public function index(): JsonResponse
    {
        $clubs = $this->clubRepository->getAll();

        return $this->success([
            'clubs' => ClubResource::collection($clubs),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $club = $this->clubRepository->findById($id);

        return $this->success([
            'club' => new ClubResource($club),
        ]);
    }

    public function store(StoreClubRequest $request): JsonResponse
    {
        $club = $this->clubRepository->create($request->user(), $request->validated());

        return $this->success([
            'club' => new ClubResource($club),
        ], 'club.created', 201);
    }

    public function update(UpdateClubRequest $request, Club $club): JsonResponse
    {
        $club = $this->clubRepository->update($request->user(), $club, $request->validated());

        return $this->success([
            'club' => new ClubResource($club),
        ], 'club.updated');
    }

    public function destroy(Request $request, Club $club): JsonResponse
    {
        $this->clubRepository->delete($request->user(), $club);

        return $this->success([], 'club.deleted');
    }
}

