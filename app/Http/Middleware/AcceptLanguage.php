<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptLanguage
{
    private const ALLOWED = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language', config('app.locale'));

        // Accept "en", "ar", "en-US", "ar-EG" etc.; take first part
        if (str_contains($locale, '-')) {
            $locale = strtolower(substr($locale, 0, strpos($locale, '-')));
        } else {
            $locale = strtolower(substr($locale, 0, 2));
        }

        if (in_array($locale, self::ALLOWED, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
