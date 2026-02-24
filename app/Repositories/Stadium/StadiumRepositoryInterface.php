<?php

namespace App\Repositories\Stadium;

use App\Models\Stadium;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StadiumRepositoryInterface
{
    public function getAll(): LengthAwarePaginator;

    public function findById(int $id): Stadium;

    public function create(array $data): Stadium;

    public function update(Stadium $stadium, array $data): Stadium;

    public function delete(Stadium $stadium): void;
}
