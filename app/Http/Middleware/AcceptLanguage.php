<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptLanguage
{
    private const ALLOWED = ['en', 'ar'];

    public static function resolveLocale(Request $request): ?string
    {
        $header = $request->header('Accept-Language');
        $raw = is_string($header) && $header !== '' ? $header : (string) config('app.locale', 'en');

        // Examples:
        // - "en"
        // - "ar-EG"
        // - "en-US,en;q=0.9,ar;q=0.8"
        $first = trim(strtok($raw, ',') ?: $raw);
        $first = trim(strtok($first, ';') ?: $first);

        if ($first === '') {
            return null;
        }

        $first = strtolower($first);
        $base = str_contains($first, '-') ? substr($first, 0, (int) strpos($first, '-')) : substr($first, 0, 2);

        return in_array($base, self::ALLOWED, true) ? $base : null;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $locale = self::resolveLocale($request);
        if ($locale) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
