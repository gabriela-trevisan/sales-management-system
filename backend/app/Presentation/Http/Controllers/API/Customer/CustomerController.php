<?php

namespace App\Presentation\Http\Controllers\API\Customer;

use App\Application\Customer\CreateCustomer\CreateCustomerCommand;
use App\Application\Customer\CreateCustomer\CreateCustomerHandler;
use App\Application\Customer\UpdateCustomer\UpdateCustomerCommand;
use App\Application\Customer\UpdateCustomer\UpdateCustomerHandler;
use App\Domain\Customer\Contracts\CustomerRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Customer\CreateCustomerRequest;
use App\Presentation\Http\Requests\Customer\UpdateCustomerRequest;
use App\Presentation\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CreateCustomerHandler $createCustomerHandler,
        private UpdateCustomerHandler $updateCustomerHandler,
    ) {}

    #[OA\Get(
        path: "/api/customers",
        summary: "Listar clientes",
        description: "Retorna lista paginada de clientes com filtros opcionais",
        security: [["bearerAuth" => []]],
        tags: ["Customers"],
        parameters: [
            new OA\Parameter(
                name: "status",
                in: "query",
                description: "Filtrar por status",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["active", "inactive", "prospect", "churned"])
            ),
            new OA\Parameter(
                name: "assigned_to",
                in: "query",
                description: "Filtrar por vendedor responsável (ID)",
                required: false,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "search",
                in: "query",
                description: "Buscar por nome, email ou documento",
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
                description: "Lista de clientes",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Customer")
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
            'status' => $request->get('status'),
            'assigned_to' => $request->get('assigned_to'),
            'search' => $request->get('search'),
        ];

        $customers = $this->customerRepository->getAll(
            array_filter($filters),
            $request->get('per_page', 15)
        );

        return response()->json([
            'data' => CustomerResource::collection($customers->items()),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
            ],
        ]);
    }

    #[OA\Post(
        path: "/api/customers",
        summary: "Criar novo cliente",
        description: "Cria um novo cliente no sistema",
        security: [["bearerAuth" => []]],
        tags: ["Customers"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "document", "email"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "João Silva"),
                    new OA\Property(property: "document", type: "string", example: "123.456.789-00"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "joao@example.com"),
                    new OA\Property(property: "phone", type: "string", nullable: true, example: "(11) 98765-4321"),
                    new OA\Property(property: "status", type: "string", enum: ["active", "inactive", "prospect", "churned"], example: "active"),
                    new OA\Property(property: "assigned_to", type: "integer", nullable: true, example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Cliente criado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Cliente criado com sucesso"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Customer")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 422, description: "Dados inválidos")
        ]
    )]
    public function store(CreateCustomerRequest $request): JsonResponse
    {
        $command = new CreateCustomerCommand(
            name: $request->input('name'),
            document: $request->input('document'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            status: $request->input('status', 'active'),
            segmentId: $request->input('segment_id'),
            assignedTo: $request->user()->id, // auto-atribui ao usuário logado
        );

        $customer = $this->createCustomerHandler->handle($command);

        // invalida cache do dashboard pois o total de clientes mudou
        Cache::forget('dashboard.metrics');

        return response()->json([
            'message' => 'Cliente criado com sucesso',
            'data' => new CustomerResource($customer),
        ], 201);
    }

    #[OA\Get(
        path: "/api/customers/{id}",
        summary: "Buscar cliente por ID",
        description: "Retorna os dados completos de um cliente específico",
        security: [["bearerAuth" => []]],
        tags: ["Customers"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID do cliente",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Dados do cliente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/Customer")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Cliente não encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Cliente não encontrado")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado")
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return response()->json([
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        $this->authorize('view', $customer);

        return response()->json([
            'data' => new CustomerResource($customer),
        ]);
    }

    #[OA\Put(
        path: "/api/customers/{id}",
        summary: "Atualizar cliente",
        description: "Atualiza os dados de um cliente existente",
        security: [["bearerAuth" => []]],
        tags: ["Customers"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID do cliente",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "João Silva"),
                    new OA\Property(property: "document", type: "string", example: "123.456.789-00"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "joao@example.com"),
                    new OA\Property(property: "phone", type: "string", nullable: true, example: "(11) 98765-4321"),
                    new OA\Property(property: "status", type: "string", enum: ["active", "inactive", "prospect", "churned"], example: "active"),
                    new OA\Property(property: "assigned_to", type: "integer", nullable: true, example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Cliente atualizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Cliente atualizado com sucesso"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Customer")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Cliente não encontrado"),
            new OA\Response(response: 422, description: "Dados inválidos")
        ]
    )]
    public function update(UpdateCustomerRequest $request, int $id): JsonResponse
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $this->authorize('update', $customer);

        $validated = $request->validated();

        $command = new UpdateCustomerCommand(
            id: $id,
            name: $validated['name'] ?? null,
            document: $validated['document'] ?? null,
            email: $validated['email'] ?? null,
            phone: $validated['phone'] ?? null,
            hasPhone: array_key_exists('phone', $validated),
            status: $validated['status'] ?? null,
            segmentId: $validated['segment_id'] ?? null,
            hasSegmentId: array_key_exists('segment_id', $validated),
        );

        $customer = $this->updateCustomerHandler->handle($command);

        // invalida cache do dashboard pois métricas agregadas do período podem ter mudado
        Cache::forget('dashboard.metrics');

        return response()->json([
            'message' => 'Cliente atualizado com sucesso',
            'data' => new CustomerResource($customer),
        ]);
    }

    #[OA\Delete(
        path: "/api/customers/{id}",
        summary: "Remover cliente",
        description: "Remove um cliente do sistema",
        security: [["bearerAuth" => []]],
        tags: ["Customers"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID do cliente",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cliente removido com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Cliente removido com sucesso")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Cliente não encontrado")
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $this->authorize('delete', $customer);

        $this->customerRepository->delete($id);

        // invalida cache do dashboard pois o total de clientes mudou
        Cache::forget('dashboard.metrics');

        return response()->json([
            'message' => 'Cliente removido com sucesso',
        ]);
    }
}
