<?php

namespace App\Presentation\Http\Controllers\API\Customer;

use App\Application\Customer\CreateCustomer\CreateCustomerCommand;
use App\Application\Customer\CreateCustomer\CreateCustomerHandler;
use App\Domain\Customer\Contracts\CustomerRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Customer\CreateCustomerRequest;
use App\Presentation\Http\Requests\Customer\UpdateCustomerRequest;
use App\Presentation\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CreateCustomerHandler $createCustomerHandler
    ) {}

    /**
     * Display a listing of customers
     */
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

    /**
     * Store a newly created customer
     */
    public function store(CreateCustomerRequest $request): JsonResponse
    {
        $command = new CreateCustomerCommand(
            name: $request->input('name'),
            document: $request->input('document'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            status: $request->input('status', 'active'),
            assignedTo: $request->input('assigned_to'),
        );

        $customer = $this->createCustomerHandler->handle($command);

        return response()->json([
            'message' => 'Cliente criado com sucesso',
            'data' => new CustomerResource($customer),
        ], 201);
    }

    /**
     * Display the specified customer
     */
    public function show(int $id): JsonResponse
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return response()->json([
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        return response()->json([
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Update the specified customer
     */
    public function update(UpdateCustomerRequest $request, int $id): JsonResponse
    {
        $customer = $this->customerRepository->update($id, $request->validated());

        return response()->json([
            'message' => 'Cliente atualizado com sucesso',
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Remove the specified customer
     */
    public function destroy(int $id): JsonResponse
    {
        $this->customerRepository->delete($id);

        return response()->json([
            'message' => 'Cliente removido com sucesso',
        ]);
    }
}
