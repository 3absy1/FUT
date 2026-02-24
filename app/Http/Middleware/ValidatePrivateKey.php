<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePrivateKey
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $key = config('app.api_private_key');

        if (empty($key)) {
            return $next($request);
        }

        $headerKey = $request->header('X-Api-Key') ?? $request->header('X-Private-Key');

        if ($headerKey !== $key) {
            return $this->error('invalid_api_key', 'error', 'UNAUTHORIZED', [], 401);
        }

        return $next($request);
    }
}
