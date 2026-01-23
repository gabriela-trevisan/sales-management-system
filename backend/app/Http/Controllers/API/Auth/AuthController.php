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
        summary: "Autenticar usuário",
        description: "Realiza login do usuário e retorna token JWT",
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
                        ),
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
    public function login(Request $request)
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

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
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
        $request->user()->currentAccessToken()->delete();

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
    public function refresh(Request $request)
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
}
