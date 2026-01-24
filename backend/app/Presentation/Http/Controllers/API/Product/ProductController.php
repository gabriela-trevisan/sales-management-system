<?php

namespace App\Presentation\Http\Controllers\API\Product;

use App\Application\Product\CreateProduct\CreateProductCommand;
use App\Application\Product\CreateProduct\CreateProductHandler;
use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Product\CreateProductRequest;
use App\Presentation\Http\Requests\Product\UpdateProductRequest;
use App\Presentation\Http\Resources\Product\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CreateProductHandler $createProductHandler
    ) {}

    #[OA\Get(
        path: "/api/products",
        summary: "Listar produtos",
        description: "Retorna lista paginada de produtos com filtros opcionais",
        security: [["bearerAuth" => []]],
        tags: ["Products"],
        parameters: [
            new OA\Parameter(
                name: "is_active",
                in: "query",
                description: "Filtrar por status (true/false)",
                required: false,
                schema: new OA\Schema(type: "boolean")
            ),
            new OA\Parameter(
                name: "category_id",
                in: "query",
                description: "Filtrar por categoria (ID)",
                required: false,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "search",
                in: "query",
                description: "Buscar por nome, SKU ou descrição",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                description: "Itens por página",
                required: false,
                schema: new OA\Schema(type: "integer", default: 15)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de produtos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Product")
                        ),
                        new OA\Property(
                            property: "meta",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer"),
                                new OA\Property(property: "last_page", type: "integer"),
                                new OA\Property(property: "per_page", type: "integer"),
                                new OA\Property(property: "total", type: "integer")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'is_active' => $request->get('is_active'),
            'category_id' => $request->get('category_id'),
            'search' => $request->get('search'),
        ];

        $products = $this->productRepository->getAll(
            array_filter($filters, fn($value) => $value !== null),
            $request->get('per_page', 15)
        );

        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    #[OA\Post(
        path: "/api/products",
        summary: "Criar novo produto",
        description: "Cria um novo produto no sistema",
        security: [["bearerAuth" => []]],
        tags: ["Products"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "sku", "base_price", "unit"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Hora de Desenvolvimento Senior"),
                    new OA\Property(property: "sku", type: "string", example: "DEV-SENIOR-HOUR"),
                    new OA\Property(property: "description", type: "string", nullable: true, example: "Hora de desenvolvimento com profissional sênior"),
                    new OA\Property(property: "category_id", type: "integer", nullable: true, example: 1),
                    new OA\Property(property: "base_price", type: "number", format: "float", example: 250.00),
                    new OA\Property(property: "cost_price", type: "number", format: "float", nullable: true, example: 150.00),
                    new OA\Property(property: "unit", type: "string", enum: ["unit", "kg", "liter", "meter", "hour", "month"], example: "hour"),
                    new OA\Property(property: "is_active", type: "boolean", example: true),
                    new OA\Property(property: "requires_approval", type: "boolean", example: false),
                    new OA\Property(property: "specifications", type: "object", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Produto criado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Produto criado com sucesso"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Product")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 422, description: "Dados inválidos")
        ]
    )]
    public function store(CreateProductRequest $request): JsonResponse
    {
        $command = new CreateProductCommand(
            name: $request->input('name'),
            sku: $request->input('sku'),
            basePrice: (float) $request->input('base_price'),
            description: $request->input('description'),
            categoryId: $request->input('category_id'),
            costPrice: $request->input('cost_price') ? (float) $request->input('cost_price') : null,
            unit: $request->input('unit', 'unit'),
            isActive: $request->input('is_active', true),
            requiresApproval: $request->input('requires_approval', false),
            specifications: $request->input('specifications'),
        );

        $product = $this->createProductHandler->handle($command);
        $product->load('category');

        return response()->json([
            'message' => 'Produto criado com sucesso',
            'data' => new ProductResource($product),
        ], 201);
    }

    #[OA\Get(
        path: "/api/products/{id}",
        summary: "Detalhes do produto",
        description: "Retorna os detalhes de um produto específico",
        security: [["bearerAuth" => []]],
        tags: ["Products"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID do produto",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detalhes do produto",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/Product")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Produto não encontrado")
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produto não encontrado',
            ], 404);
        }

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    #[OA\Put(
        path: "/api/products/{id}",
        summary: "Atualizar produto",
        description: "Atualiza os dados de um produto existente",
        security: [["bearerAuth" => []]],
        tags: ["Products"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID do produto",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "sku", type: "string"),
                    new OA\Property(property: "description", type: "string", nullable: true),
                    new OA\Property(property: "category_id", type: "integer", nullable: true),
                    new OA\Property(property: "base_price", type: "number", format: "float"),
                    new OA\Property(property: "cost_price", type: "number", format: "float", nullable: true),
                    new OA\Property(property: "unit", type: "string"),
                    new OA\Property(property: "is_active", type: "boolean"),
                    new OA\Property(property: "requires_approval", type: "boolean"),
                    new OA\Property(property: "specifications", type: "object", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Produto atualizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Produto atualizado com sucesso"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Product")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Produto não encontrado"),
            new OA\Response(response: 422, description: "Dados inválidos")
        ]
    )]
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productRepository->update($id, $request->validated());

        return response()->json([
            'message' => 'Produto atualizado com sucesso',
            'data' => new ProductResource($product),
        ]);
    }

    #[OA\Delete(
        path: "/api/products/{id}",
        summary: "Deletar produto",
        description: "Remove um produto do sistema (soft delete)",
        security: [["bearerAuth" => []]],
        tags: ["Products"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID do produto",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Produto deletado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Produto deletado com sucesso")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Produto não encontrado")
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->productRepository->delete($id);

        return response()->json([
            'message' => 'Produto deletado com sucesso',
        ]);
    }
}
