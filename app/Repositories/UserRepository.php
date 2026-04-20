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
            'birth_date' => $data['birth_date'] ?? $user->birth_date,
            'fcm_token'  => $data['fcm_token'] ?? $user->fcm_token,
            'area_id'    => array_key_exists('area_id', $data) ? $data['area_id'] : $user->area_id,
            'position' => $data['position'] ?? $user->position,
            'pac' => $data['pac'] ?? $user->pac,
            'sho' => $data['sho'] ?? $user->sho,
            'pas' => $data['pas'] ?? $user->pas,
            'dri' => $data['dri'] ?? $user->dri,
            'def' => $data['def'] ?? $user->def,
            'phy' => $data['phy'] ?? $user->phy,
            'gk_diving' => $data['gk_diving'] ?? $user->gk_diving,
            'gk_handling' => $data['gk_handling'] ?? $user->gk_handling,
            'gk_kicking' => $data['gk_kicking'] ?? $user->gk_kicking,
            'gk_reflexes' => $data['gk_reflexes'] ?? $user->gk_reflexes,
            'gk_positioning' => $data['gk_positioning'] ?? $user->gk_positioning,
            'goals_scored' => $data['goals_scored'] ?? $user->goals_scored,
            'assists_count' => $data['assists_count'] ?? $user->assists_count,
        ]);

        return $user->fresh();
    }
}
