<?php

namespace App\Domain\Product\Contracts;

use App\Domain\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    /**
     * Get all products with filters and pagination.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator<int, Product>
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a product by ID.
     *
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product;

    /**
     * Create a new product.
     *
     * @param array<string, mixed> $data
     * @return Product
     */
    public function create(array $data): Product;

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return Product
     */
    public function update(int $id, array $data): Product;

    /**
     * Delete a product (soft delete).
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
