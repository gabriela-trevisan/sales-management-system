<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Customer\Contracts\CustomerRepositoryInterface;
use App\Domain\Customer\Models\Customer;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer
    {
        return Customer::with(['addresses', 'contacts', 'assignedUser', 'segment'])->find($id);
    }

    public function findByDocument(string $document): ?Customer
    {
        return Customer::where('document', $document)->first();
    }

    /**
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Customer>
     */
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = Customer::with(['assignedUser', 'segment']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            // Remove formatação para buscar documento
            $searchClean = preg_replace('/[^0-9]/', '', $search);
            
            $query->where(function ($q) use ($search, $searchClean) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
                
                // Busca por documento (já armazenado sem formatação)
                if ($searchClean) {
                    $q->orWhere('document', 'like', "%{$searchClean}%");
                }
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * @param array<string, mixed> $data
     * @return Customer
     */
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Customer
     */
    public function update(int $id, array $data): Customer
    {
        $customer = $this->findById($id);
        $customer->update($data);
        return $customer->fresh();
    }

    public function delete(int $id): bool
    {
        $customer = $this->findById($id);
        return $customer->delete();
    }

    public function getByAssignedUser(int $userId)
    {
        return Customer::where('assigned_to', $userId)
            ->with(['addresses', 'contacts'])
            ->get();
    }
}
