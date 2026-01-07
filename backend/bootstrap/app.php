<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;

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
        
        // Desabilitar redirecionamento para login em rotas API
        $middleware->redirectGuestsTo(fn (Request $request) => 
            $request->expectsJson() ? null : route('login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Retornar JSON para erros de autenticação em rotas API
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Não autenticado. Por favor, faça login para acessar este recurso.'
                ], 401);
            }
        });

        // Retornar JSON para rotas não encontradas em API
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Rota não encontrada.'
                ], 404);
            }
        });
    })->create();
