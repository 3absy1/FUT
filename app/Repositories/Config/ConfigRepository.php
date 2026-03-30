<?php

namespace App\Repositories\Config;

use App\Models\Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ConfigRepository implements ConfigRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return Config::query()
            ->latest()
            ->paginate(10);
    }

    public function findById(int $id): Config
    {
        return Config::query()->findOrFail($id);
    }

    public function create(array $data): Config
    {
        return Config::query()->create($data);
    }

    public function update(Config $config, array $data): Config
    {
        $config->update($data);

        return $config->fresh();
    }

    public function delete(Config $config): void
    {
        $config->delete();
    }
}

