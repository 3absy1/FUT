<?php

namespace App\Repositories\Stadium;

use App\Models\Stadium;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StadiumRepository implements StadiumRepositoryInterface
{
    public function getAll(): LengthAwarePaginator
    {
        return Stadium::with('area')
            ->latest()
            ->paginate(10);
    }

    public function findById(int $id): Stadium
    {
        return Stadium::with('area')->findOrFail($id);
    }

    public function create(array $data): Stadium
    {
        return Stadium::create($data);
    }

    public function update(Stadium $stadium, array $data): Stadium
    {
        $stadium->update($data);
        return $stadium->fresh();
    }

    public function delete(Stadium $stadium): void
    {
        $stadium->delete();
    }
}
