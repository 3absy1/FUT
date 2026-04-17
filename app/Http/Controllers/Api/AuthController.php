<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\Auth\AuthRepositoryInterface;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private AuthRepositoryInterface $authRepository
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authRepository->register($request->validated());

        return $this->success(
            ['user' => new UserResource($user)],
            'auth.register_success',
            201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authRepository->login($request->validated());

        if ($result['requires_otp'] ?? false) {
            return $this->error(
                'auth.requires_otp',
                'auth.requires_otp',
                '403',
                [__('api.auth.requires_otp')],
                403,
                // [
                //     'requires_otp' => true,
                //     'user' => new UserResource($result['user']),
                // ]
            );
        }

        return $this->success([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'user' => new UserResource($result['user']),
        ], 'auth.login_success');
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authRepository->verifyOtp($request->validated());

        return $this->success([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'user' => new UserResource($result['user']),
        ], 'auth.verify_success');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authRepository->forgotPassword($request->validated());

        return $this->success([], 'auth.password_reset_otp_sent');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authRepository->resetPassword($request->validated());

        return $this->success([], 'auth.password_reset_success');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authRepository->logout($request->user());

        return $this->success([], 'auth.logout_success');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success([
            'user' => new UserResource($request->user()),
        ]);
    }
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->authRepository->updateProfile(
            $request->user(),
            $request->validated()
        );

        return $this->success([
            'user' => new UserResource($user),
        ], 'auth.profile_updated');
    }
}
