<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthRepository implements AuthRepositoryInterface
{
    private const DUMMY_OTP = '1111';

    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'nick_name' => $data['nick_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'age' => $data['age'],
            'password' => $data['password'],
            'fcm_token' => $data['fcm_token'] ?? null,
            'otp' => self::DUMMY_OTP,
            'otp_expires_at' => now()->addMinutes(10),
            'is_verified' => false,
        ]);
    }

    public function login(array $data): array
    {
        $user = User::where('phone', $data['phone'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => [__('api.auth.credentials_incorrect')],
            ]);
        }

        if (! $user->is_verified) {
            return [
                'requires_otp' => true,
                'user' => $user,
            ];
        }

        $token = $user->createToken('auth')->plainTextToken;

        return [
            'requires_otp' => false,
            'token' => $token,
            'user' => $user,
        ];
    }

    public function verifyOtp(array $data): array
    {
        $user = User::where('phone', $data['phone'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => [__('api.auth.user_not_found')],
            ]);
        }

        if (! $user->acceptsDummyOtp($data['otp'])) {
            throw ValidationException::withMessages([
                'otp' => [__('api.auth.invalid_otp')],
            ]);
        }

        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        $token = $user->createToken('auth')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

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
