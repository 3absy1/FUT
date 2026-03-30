<?php

namespace App\Repositories\Config;

use App\Models\Config;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ConfigRepositoryInterface
{
    public function getAll(): LengthAwarePaginator;

    public function findById(int $id): Config;

    public function create(array $data): Config;

    public function update(Config $config, array $data): Config;

    public function delete(Config $config): void;
}

