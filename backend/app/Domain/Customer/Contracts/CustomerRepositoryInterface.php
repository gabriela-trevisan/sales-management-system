<?php

namespace App\Domain\Customer\Contracts;

use App\Domain\Customer\Models\Customer;

interface CustomerRepositoryInterface
{
    /**
     * Find a customer by ID
     */
    public function findById(int $id): ?Customer;

    /**
     * Find a customer by document (CPF/CNPJ)
     */
    public function findByDocument(string $document): ?Customer;

    /**
     * Get all customers with optional filters
     */
    public function getAll(array $filters = [], int $perPage = 15);

    /**
     * Create a new customer
     */
    public function create(array $data): Customer;

    /**
     * Update a customer
     */
    public function update(int $id, array $data): Customer;

    /**
     * Delete a customer
     */
    public function delete(int $id): bool;

    /**
     * Get customers assigned to a user
     */
    public function getByAssignedUser(int $userId);
}
