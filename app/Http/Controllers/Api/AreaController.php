<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Area\StoreAreaRequest;
use App\Http\Requests\Area\UpdateAreaRequest;
use App\Http\Resources\AreaResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Area;
use App\Repositories\Area\AreaRepositoryInterface;
use Illuminate\Http\JsonResponse;

class AreaController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private AreaRepositoryInterface $areaRepository
    ) {}

    public function index(): JsonResponse
    {
        $areas = $this->areaRepository->getAll();

        return $this->success([
            'areas' => AreaResource::collection($areas),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $area = $this->areaRepository->findById($id);

        return $this->success([
            'area' => new AreaResource($area),
        ]);
    }

    public function store(StoreAreaRequest $request): JsonResponse
    {
        $area = $this->areaRepository->create($request->validated());

        return $this->success([
            'area' => new AreaResource($area),
        ], 'area.created', 201);
    }

    public function update(UpdateAreaRequest $request, Area $area): JsonResponse
    {
        $area = $this->areaRepository->update($area, $request->validated());

        return $this->success([
            'area' => new AreaResource($area),
        ], 'area.updated');
    }

    public function destroy(Area $area): JsonResponse
    {
        $this->areaRepository->delete($area);

        return $this->success([], 'area.deleted');
    }
}

