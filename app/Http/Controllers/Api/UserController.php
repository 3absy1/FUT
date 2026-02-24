<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\UserRepositoryInterface;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private UserRepositoryInterface $authRepository
    ) {}

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
