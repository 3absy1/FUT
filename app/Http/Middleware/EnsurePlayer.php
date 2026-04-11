<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlayer
{
    /**
     * Block stadium owner accounts from player-app routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->is_stadium_owner) {
            $msg = __('api.auth.player_route_only');

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
