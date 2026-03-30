<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Config\StoreConfigRequest;
use App\Http\Requests\Config\UpdateConfigRequest;
use App\Http\Resources\ConfigResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Config;
use App\Repositories\Config\ConfigRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private ConfigRepositoryInterface $configRepository
    ) {}

    public function index(): JsonResponse
    {
        $configs = $this->configRepository->getAll();

        return $this->success([
            'configs' => ConfigResource::collection($configs),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $config = $this->configRepository->findById($id);

        return $this->success([
            'config' => new ConfigResource($config),
        ]);
    }

    public function store(StoreConfigRequest $request): JsonResponse
    {
        $config = $this->configRepository->create($request->validated());

        return $this->success([
            'config' => new ConfigResource($config),
        ], 'config.created', 201);
    }

    public function update(
        UpdateConfigRequest $request,
        Config $config
    ): JsonResponse {
        $config = $this->configRepository->update($config, $request->validated());

        return $this->success([
            'config' => new ConfigResource($config),
        ], 'config.updated');
    }

    public function destroy(Config $config): JsonResponse
    {
        $this->configRepository->delete($config);

        return $this->success([], 'config.deleted');
    }
}

