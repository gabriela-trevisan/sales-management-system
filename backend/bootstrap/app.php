<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        
        // Security Headers para todas as requisições
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        
        // Desabilitar redirecionamento para login em rotas API
        $middleware->redirectGuestsTo(fn (Request $request) => 
            $request->expectsJson() ? null : route('login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /**
         * Handler global de exceções com formato RFC 7807 (Problem Details for HTTP APIs)
         * 
         * Formato padronizado:
         * {
         *   "type": "https://example.com/errors/validation-failed",
         *   "title": "Validation Failed",
         *   "status": 422,
         *   "detail": "Os dados fornecidos são inválidos",
         *   "instance": "/api/customers",
         *   "timestamp": "2026-01-17T23:45:00Z",
         *   "errors": { ... } // Apenas para ValidationException
         * }
         */
        
        // Validation Errors (422)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'type' => url('/docs/errors/validation-failed'),
                    'title' => 'Validation Failed',
                    'status' => 422,
                    'detail' => $e->getMessage() ?: 'Os dados fornecidos são inválidos',
                    'instance' => $request->path(),
                    'timestamp' => now()->toIso8601String(),
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Authentication Errors (401)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'type' => url('/docs/errors/unauthenticated'),
                    'title' => 'Unauthenticated',
                    'status' => 401,
                    'detail' => 'Não autenticado. Por favor, faça login para acessar este recurso.',
                    'instance' => $request->path(),
                    'timestamp' => now()->toIso8601String(),
                ], 401);
            }
        });

        // Rate Limiting / Too Many Requests (429)
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
                return response()->json([
                    'type' => url('/docs/errors/rate-limit-exceeded'),
                    'title' => 'Too Many Requests',
                    'status' => 429,
                    'detail' => 'Você excedeu o limite de requisições. Por favor, aguarde antes de tentar novamente.',
                    'instance' => $request->path(),
                    'timestamp' => now()->toIso8601String(),
                    'retry_after' => $retryAfter,
                ], 429, ['Retry-After' => $retryAfter]);
            }
        });

        // Model Not Found (404)
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $modelName = class_basename($e->getModel());
                return response()->json([
                    'type' => url('/docs/errors/not-found'),
                    'title' => 'Resource Not Found',
                    'status' => 404,
                    'detail' => "{$modelName} não encontrado",
                    'instance' => $request->path(),
                    'timestamp' => now()->toIso8601String(),
                ], 404);
            }
        });

        // Route Not Found (404)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'type' => url('/docs/errors/not-found'),
                    'title' => 'Not Found',
                    'status' => 404,
                    'detail' => 'Rota não encontrada',
                    'instance' => $request->path(),
                    'timestamp' => now()->toIso8601String(),
                ], 404);
            }
        });

        // HTTP Exceptions (400, 403, 405, 500, etc)
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = $e->getStatusCode();
                $titles = [
                    400 => 'Bad Request',
                    403 => 'Forbidden',
                    405 => 'Method Not Allowed',
                    500 => 'Internal Server Error',
                    503 => 'Service Unavailable',
                ];

                return response()->json([
                    'type' => url("/docs/errors/http-{$statusCode}"),
                    'title' => $titles[$statusCode] ?? 'HTTP Error',
                    'status' => $statusCode,
                    'detail' => $e->getMessage() ?: 'Ocorreu um erro no servidor',
                    'instance' => $request->path(),
                    'timestamp' => now()->toIso8601String(),
                ], $statusCode);
            }
        });

        // Generic Exception Handler (500)
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Em produção, não expõe detalhes internos
                $detail = app()->environment('production')
                    ? 'Ocorreu um erro interno no servidor'
                    : $e->getMessage();

                return response()->json([
                    'type' => url('/docs/errors/server-error'),
                    'title' => 'Internal Server Error',
                    'status' => 500,
                    'detail' => $detail,
                    'instance' => $request->path(),
                    'timestamp' => now()->toIso8601String(),
                    // Debug info apenas em desenvolvimento
                    ...(!app()->environment('production') ? [
                        'debug' => [
                            'exception' => get_class($e),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => collect($e->getTrace())->take(3)->toArray(),
                        ]
                    ] : []),
                ], 500);
            }
        });
    })->create();
