<?php

namespace App\Repositories\Auth;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function register(array $data): User;

    public function registerStadiumOwner(array $data): User;

    public function login(array $data): array;

    public function loginStadiumOwner(array $data): array;

    public function verifyOtp(array $data): array;

    public function verifyOtpStadiumOwner(array $data): array;

    public function forgotPassword(array $data): void;

    public function forgotPasswordStadiumOwner(array $data): void;

    public function resetPassword(array $data): void;

    public function resetPasswordStadiumOwner(array $data): void;

    public function logout(User $user): void;

    public function updateProfile(User $user, array $data): User;
}
