<?php

use App\Contracts\HuntingBooking\HuntingBookingExceptionInterface;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1'
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {

            if ($e instanceof HuntingBookingExceptionInterface) {
                return response()->json([
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                    'errors' => $e->getErrors(),
                    'code' => 422,
                ], 422);
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                    'code' => $e->status ?? 422,
                ], $e->status ?? 422);
            }

            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'status' => 'fail',
                    'message' => $e->getMessage() ?: 'HTTP Error',
                    'errors' => null,
                    'code' => $e->getStatusCode(),
                ], $e->getStatusCode());
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error.',
                'errors' => null,
                'code' => 500,
            ], 500);
        });
    })->create();
