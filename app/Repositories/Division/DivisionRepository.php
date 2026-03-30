<?php

namespace App\Repositories\Division;

use App\Models\Division;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DivisionRepository implements DivisionRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return Division::query()
            ->orderBy('sort_order')
            ->paginate(10);
    }

    public function findById(int $id): Division
    {
        return Division::query()->findOrFail($id);
    }

    public function create(array $data): Division
    {
        return Division::query()->create($data);
    }

    public function update(Division $division, array $data): Division
    {
        $division->update($data);

        return $division->fresh();
    }

    public function delete(Division $division): void
    {
        $division->delete();
    }
}

