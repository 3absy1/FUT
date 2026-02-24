<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function updateProfile(User $user, array $data): User;
}
