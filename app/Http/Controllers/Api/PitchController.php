<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pitch\StorePitchRequest;
use App\Http\Requests\Pitch\UpdatePitchRequest;
use App\Http\Resources\PitchResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Pitch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PitchController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $stadiumId = $request->user()->stadium_id;
        $pitches = Pitch::query()
            ->where('stadium_id', $stadiumId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $this->success([
            'pitches' => PitchResource::collection($pitches),
        ]);
    }

    public function store(StorePitchRequest $request): JsonResponse
    {
        $stadiumId = $request->user()->stadium_id;
        $pitch = Pitch::create([
            'stadium_id' => $stadiumId,
            'name' => $request->validated()['name'],
            'sort_order' => $request->validated()['sort_order'] ?? 0,
        ]);

        return $this->success([
            'pitch' => new PitchResource($pitch),
        ], 'pitch.created', 201);
    }

    public function update(UpdatePitchRequest $request, Pitch $pitch): JsonResponse
    {
        $this->authorizePitch($request, $pitch);

        $pitch->update($request->validated());

        return $this->success([
            'pitch' => new PitchResource($pitch->fresh()),
        ], 'pitch.updated');
    }

    public function destroy(Request $request, Pitch $pitch): JsonResponse
    {
        $this->authorizePitch($request, $pitch);

        $pitch->delete();

        return $this->success([], 'pitch.deleted');
    }

    private function authorizePitch(Request $request, Pitch $pitch): void
    {
        if ((int) $pitch->stadium_id !== (int) $request->user()->stadium_id) {
            throw ValidationException::withMessages([
                'pitch' => [__('api.pitch.not_yours')],
            ]);
        }
    }
}
