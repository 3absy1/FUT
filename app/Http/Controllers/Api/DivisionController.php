<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Division\StoreDivisionRequest;
use App\Http\Requests\Division\UpdateDivisionRequest;
use App\Http\Resources\DivisionResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Division;
use App\Repositories\Division\DivisionRepositoryInterface;
use Illuminate\Http\JsonResponse;

class DivisionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private DivisionRepositoryInterface $divisionRepository
    ) {}

    public function index(): JsonResponse
    {
        $divisions = $this->divisionRepository->getAll();

        return $this->success([
            'divisions' => DivisionResource::collection($divisions),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $division = $this->divisionRepository->findById($id);

        return $this->success([
            'division' => new DivisionResource($division),
        ]);
    }

    public function store(StoreDivisionRequest $request): JsonResponse
    {
        $division = $this->divisionRepository->create($request->validated());

        return $this->success([
            'division' => new DivisionResource($division),
        ], 'division.created', 201);
    }

    public function update(UpdateDivisionRequest $request, Division $division): JsonResponse
    {
        $division = $this->divisionRepository->update($division, $request->validated());

        return $this->success([
            'division' => new DivisionResource($division),
        ], 'division.updated');
    }

    public function destroy(Division $division): JsonResponse
    {
        $this->divisionRepository->delete($division);

        return $this->success([], 'division.deleted');
    }
}

