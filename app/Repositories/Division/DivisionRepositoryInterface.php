<?php

namespace App\Repositories\Division;

use App\Models\Division;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DivisionRepositoryInterface
{
    public function getAll(): LengthAwarePaginator;

    public function findById(int $id): Division;

    public function create(array $data): Division;

    public function update(Division $division, array $data): Division;

    public function delete(Division $division): void;
}

