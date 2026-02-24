<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stadium\StoreStadiumRequest;
use App\Http\Requests\Stadium\UpdateStadiumRequest;
use App\Http\Resources\StadiumResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Stadium;
use App\Repositories\Stadium\StadiumRepositoryInterface;
use Illuminate\Http\JsonResponse;

class StadiumController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private StadiumRepositoryInterface $stadiumRepository
    ) {}

    public function index(): JsonResponse
    {
        $stadiums = $this->stadiumRepository->getAll();

        return $this->success([
            'stadiums' => StadiumResource::collection($stadiums),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $stadium = $this->stadiumRepository->findById($id);

        return $this->success([
            'stadium' => new StadiumResource($stadium),
        ]);
    }

    public function store(StoreStadiumRequest $request): JsonResponse
    {
        $stadium = $this->stadiumRepository->create($request->validated());

        return $this->success([
            'stadium' => new StadiumResource($stadium),
        ], 'stadium.created', 201);
    }

    public function update(
        UpdateStadiumRequest $request,
        Stadium $stadium
    ): JsonResponse {
        $stadium = $this->stadiumRepository
            ->update($stadium, $request->validated());

        return $this->success([
            'stadium' => new StadiumResource($stadium),
        ], 'stadium.updated');
    }

    public function destroy(Stadium $stadium): JsonResponse
    {
        $this->stadiumRepository->delete($stadium);

        return $this->success([], 'stadium.deleted');
    }
}
