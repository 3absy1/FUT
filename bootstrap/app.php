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
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                $errors = collect($e->errors())->flatten();
                return response()->json([
                    'title' => class_basename($e),
                    'message' => $errors->first(),
                    'code' => 422,
                    'errorsList' => $errors->values()->all(),
                ], $e->status);
            }
        });
    })->create();
