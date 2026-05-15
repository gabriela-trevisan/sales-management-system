<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/auth/login",
        summary: "Autenticar usuário (SPA Cookie Auth)",
        description: "Realiza login via Sanctum SPA Cookie Authentication. Inicia sessão httpOnly — nenhum token é retornado no body. Para obter Bearer token (Swagger/mobile), use POST /api/auth/token.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@salesmanagement.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login realizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "user",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Administrador"),
                                new OA\Property(property: "email", type: "string", example: "admin@salesmanagement.com")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Credenciais inválidas",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "As credenciais fornecidas estão incorretas.")
                    ]
                )
            )
        ]
    )]
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // SPA Cookie Auth (OWASP A07:2021 — Identification and Authentication Failures)
        // Sessão httpOnly: inacessível a scripts JS mesmo em caso de XSS.
        // statefulApi() em bootstrap/app.php garante que este bloco só executa
        // para requisições vindas de SANCTUM_STATEFUL_DOMAINS.
        if ($request->hasSession()) {
            auth()->guard('web')->login($user);
            $request->session()->regenerate(); // Previne session fixation
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    #[OA\Post(
        path: "/api/auth/logout",
        summary: "Logout do usuário",
        description: "Revoga o token atual do usuário autenticado",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout realizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Logout realizado com sucesso.")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            )
        ]
    )]
    public function logout(Request $request)
    {
        // Token auth: revoga o token Sanctum atual, se houver
        $currentToken = $request->user()->currentAccessToken();
        if ($currentToken instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $currentToken->delete();
        }

        // Session auth: invalida sessão do frontend SPA
        if ($request->hasSession()) {
            auth()->guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }

    #[OA\Get(
        path: "/api/auth/me",
        summary: "Dados do usuário autenticado",
        description: "Retorna os dados completos do usuário autenticado",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Dados do usuário",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "user",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Administrador"),
                                new OA\Property(property: "email", type: "string", example: "admin@salesmanagement.com"),
                                new OA\Property(property: "email_verified_at", type: "string", format: "date-time", example: "2026-01-07T10:00:00.000000Z"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-07T10:00:00.000000Z")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            )
        ]
    )]
    public function me(Request $request)
    {
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'email_verified_at' => $request->user()->email_verified_at,
                'created_at' => $request->user()->created_at,
            ],
        ]);
    }

    #[OA\Post(
        path: "/api/auth/refresh",
        summary: "Atualizar token de autenticação",
        description: "Gera um novo token JWT e revoga o token atual",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Token renovado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "2|xyz789...")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            )
        ]
    )]
    public function refresh(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        
        // Revoga o token atual
        $request->user()->currentAccessToken()->delete();
        
        // Cria um novo token
        $newToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $newToken,
        ]);
    }

    #[OA\Post(
        path: "/api/auth/token",
        summary: "Gerar Bearer token (não-SPA)",
        description: "Gera um Bearer token para clientes não-SPA (Swagger UI, apps mobile, scripts). SPAs devem usar POST /api/auth/login com cookie httpOnly.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@salesmanagement.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token gerado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "1|abcdef123456...")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Credenciais inválidas",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "As credenciais fornecidas estão incorretas.")
                    ]
                )
            )
        ]
    )]
    public function token(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
        ]);
    }
}
