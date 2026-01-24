<?php

namespace App\Presentation\Http\Controllers\API\Product;

use App\Domain\Product\Models\ProductCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductCategoryController extends Controller
{
    #[OA\Get(
        path: "/api/product-categories",
        summary: "Listar categorias de produtos",
        description: "Retorna lista de todas as categorias de produtos disponíveis",
        security: [["bearerAuth" => []]],
        tags: ["Products"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de categorias",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Desenvolvimento"),
                                    new OA\Property(property: "description", type: "string", nullable: true, example: "Serviços de desenvolvimento de software")
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
        $categories = ProductCategory::orderBy('name')->get();

        return response()->json([
            'data' => $categories->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
            ]),
        ]);
    }
}
