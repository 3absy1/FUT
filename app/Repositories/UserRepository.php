<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function updateProfile(User $user, array $data): User
    {
        $user->update([
            'name'       => $data['name'] ?? $user->name,
            'nick_name'  => $data['nick_name'] ?? $user->nick_name,
            'email'      => $data['email'] ?? $user->email,
            'age'        => $data['age'] ?? $user->age,
            'fcm_token'  => $data['fcm_token'] ?? $user->fcm_token,
            'area_id'    => array_key_exists('area_id', $data) ? $data['area_id'] : $user->area_id,
        ]);

        return $user->fresh();
    }
}
