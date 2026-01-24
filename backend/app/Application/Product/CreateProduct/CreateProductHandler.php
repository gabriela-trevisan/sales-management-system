<?php

namespace App\Application\Product\CreateProduct;

use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Domain\Product\Models\Product;

class CreateProductHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function handle(CreateProductCommand $command): Product
    {
        $data = [
            'name' => $command->name,
            'sku' => $command->sku,
            'description' => $command->description,
            'category_id' => $command->categoryId,
            'base_price' => $command->basePrice,
            'cost_price' => $command->costPrice,
            'unit' => $command->unit,
            'is_active' => $command->isActive,
            'requires_approval' => $command->requiresApproval,
            'specifications' => $command->specifications,
        ];

        return $this->productRepository->create($data);
    }
}
