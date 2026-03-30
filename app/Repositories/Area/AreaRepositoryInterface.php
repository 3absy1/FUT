<?php

namespace App\Repositories\Area;

use App\Models\Area;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AreaRepositoryInterface
{
    public function getAll(): LengthAwarePaginator;

    public function findById(int $id): Area;

    public function create(array $data): Area;

    public function update(Area $area, array $data): Area;

    public function delete(Area $area): void;
}

