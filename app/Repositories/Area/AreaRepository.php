<?php

namespace App\Repositories\Area;

use App\Models\Area;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AreaRepository implements AreaRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return Area::query()->latest()->paginate(10);
    }

    public function findById(int $id): Area
    {
        return Area::query()->findOrFail($id);
    }

    public function create(array $data): Area
    {
        return Area::query()->create($data);
    }

    public function update(Area $area, array $data): Area
    {
        $area->update($data);

        return $area->fresh();
    }

    public function delete(Area $area): void
    {
        $area->delete();
    }
}

