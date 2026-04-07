<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.lang' => \App\Http\Middleware\AcceptLanguage::class,
            'api.key' => \App\Http\Middleware\ValidatePrivateKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $setApiLocale = function ($request): void {
            if (!$request || !method_exists($request, 'is') || !$request->is('api/*')) {
                return;
            }

            $locale = \App\Http\Middleware\AcceptLanguage::resolveLocale($request);
            if ($locale) {
                app()->setLocale($locale);
            }
        };

        $apiError = function (
            $request,
            string $messageKey,
            string $titleKey,
            string $code,
            array $errorsList,
            int $httpStatus,
            array $extra = []
        ) use ($setApiLocale) {
            if (!$request->is('api/*')) {
                return null;
            }

            $setApiLocale($request);

            return response()->json(array_merge([
                'message' => __("api.{$messageKey}"),
                'title' => __("api.{$titleKey}"),
                'code' => $code,
                'errorsList' => $errorsList,
            ], $extra), $httpStatus);
        };

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) use ($apiError) {
            $errorsList = collect($e->errors())->flatten()->values()->all();
            return $apiError(
                $request,
                'validation_failed',
                'error',
                'VALIDATION',
                $errorsList,
                $e->status
            );
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) use ($apiError) {
            return $apiError(
                $request,
                'unauthorized',
                'error',
                'UNAUTHORIZED',
                [],
                401
            );
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) use ($apiError) {
            return $apiError(
                $request,
                'forbidden',
                'error',
                'FORBIDDEN',
                [],
                403
            );
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) use ($apiError) {
            return $apiError(
                $request,
                'not_found',
                'error',
                'NOT_FOUND',
                [],
                404
            );
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) use ($apiError) {
            return $apiError(
                $request,
                'not_found',
                'error',
                'NOT_FOUND',
                [],
                404
            );
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, $request) use ($apiError) {
            $status = $e->getStatusCode();

            $messageKey = match ($status) {
                401 => 'unauthorized',
                403 => 'forbidden',
                404 => 'not_found',
                429 => 'too_many_requests',
                default => 'something_went_wrong',
            };

            $code = match ($status) {
                401 => 'UNAUTHORIZED',
                403 => 'FORBIDDEN',
                404 => 'NOT_FOUND',
                429 => 'TOO_MANY_REQUESTS',
                default => 'HTTP_ERROR',
            };

            return $apiError($request, $messageKey, 'error', $code, [], $status);
        });

        $exceptions->render(function (\Throwable $e, $request) use ($apiError) {
            return $apiError(
                $request,
                'internal_server_error',
                'error',
                'SERVER_ERROR',
                [],
                500
            );
        });
    })->create();
