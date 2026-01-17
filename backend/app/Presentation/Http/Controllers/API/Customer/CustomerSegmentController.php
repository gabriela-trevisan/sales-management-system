<?php

namespace App\Presentation\Http\Controllers\API\Customer;

use App\Domain\Customer\Models\CustomerSegment;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CustomerSegmentController extends Controller
{
    #[OA\Get(
        path: "/api/customer-segments",
        summary: "Listar segmentos de clientes",
        description: "Retorna lista de todos os segmentos de clientes",
        security: [["bearerAuth" => []]],
        tags: ["Customers"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de segmentos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Indústria e Manufatura"),
                                    new OA\Property(property: "description", type: "string", example: "Empresas do setor industrial")
                                ],
                                type: "object"
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado")
        ]
    )]
    public function index(): JsonResponse
    {
        $segments = CustomerSegment::orderBy('name')->get(['id', 'name', 'description']);

        return response()->json([
            'data' => $segments,
        ]);
    }
}
