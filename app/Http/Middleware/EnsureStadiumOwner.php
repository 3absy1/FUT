<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStadiumOwner
{
    /**
     * Allow only users linked to a stadium (owner app).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->is_stadium_owner || ! $user->stadium_id) {
            $msg = __('api.auth.stadium_owner_required');

            return response()->json([
                'message' => $msg,
                'title' => $msg,
                'code' => 403,
                'errorsList' => [$msg],
            ], 403);
        }

        return $next($request);
    }
}
