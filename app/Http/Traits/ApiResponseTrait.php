<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait ApiResponseTrait
{
    /**
     * Success response: result, message, statusCode, statusName.
     */
    protected function success(
        mixed $result = [],
        ?string $messageKey = 'success',
        int $statusCode = 200,
        ?string $statusNameKey = 'ok'
    ): JsonResponse {
        return response()->json([
            'result' => $result,
            'message' => __("api.{$messageKey}"),
            'statusCode' => $statusCode,
            'statusName' => __("api.{$statusNameKey}"),
        ], $statusCode);
    }

    /**
     * Error response: message, title, code, errorsList. Optional $extra merged into payload.
     */
    protected function error(
        ?string $messageKey = 'something_went_wrong',
        ?string $titleKey = 'error',
        string $code = '1',
        array $errorsList = [],
        int $httpStatus = 422,
        array $extra = []
    ): JsonResponse {
        $payload = array_merge([
            'message' => $messageKey ? __("api.{$messageKey}") : __('api.something_went_wrong'),
            'title' => $titleKey ? __("api.{$titleKey}") : __('api.error'),
            'code' => $code,
            'errorsList' => $errorsList,
        ], $extra);
        return response()->json($payload, $httpStatus);
    }

    /**
     * Build validation error response in app format (errorsList from validator).
     */
    protected function validationErrorResponse(ValidationException $e): JsonResponse
    {
        $errorsList = collect($e->errors())->flatten()->values()->all();
        $first = $errorsList[0] ?? __('api.validation_failed');

        return response()->json([
            'message' => $first,
            'title' => $first,
            'code' => 422,
            'errorsList' => $errorsList,
        ], 422);
    }
}
