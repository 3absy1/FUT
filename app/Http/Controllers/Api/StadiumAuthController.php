<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\RegisterStadiumOwnerRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\StadiumOwnerResource;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StadiumAuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private AuthRepositoryInterface $authRepository
    ) {}

    public function register(RegisterStadiumOwnerRequest $request): JsonResponse
    {
        $user = $this->authRepository->registerStadiumOwner($request->validated());

        return $this->success(
            ['user' => new StadiumOwnerResource($user->load(['stadium.area', 'stadium.pitches']))],
            'auth.stadium_register_success',
            201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authRepository->loginStadiumOwner($request->validated());

        if ($result['requires_otp'] ?? false) {
            return $this->error(
                'auth.requires_otp',
                'error',
                'REQUIRES_OTP',
                [__('api.auth.requires_otp')],
                403,
                [
                    'requires_otp' => true,
                    'user' => new StadiumOwnerResource($result['user']->load(['stadium.area', 'stadium.pitches'])),
                ]
            );
        }

        return $this->success([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'user' => new StadiumOwnerResource($result['user']->load(['stadium.area', 'stadium.pitches'])),
        ], 'auth.stadium_login_success');
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authRepository->verifyOtpStadiumOwner($request->validated());

        return $this->success([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'user' => new StadiumOwnerResource($result['user']->load(['stadium.area', 'stadium.pitches'])),
        ], 'auth.stadium_verify_success');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authRepository->forgotPasswordStadiumOwner($request->validated());

        return $this->success([], 'auth.password_reset_otp_sent');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authRepository->resetPasswordStadiumOwner($request->validated());

        return $this->success([], 'auth.password_reset_success');
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['stadium.area', 'stadium.pitches']);

        return $this->success([
            'user' => new StadiumOwnerResource($user),
        ]);
    }
}
