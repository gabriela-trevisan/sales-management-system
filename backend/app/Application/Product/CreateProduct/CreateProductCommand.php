<?php

namespace App\Application\Product\CreateProduct;

class CreateProductCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $sku,
        public readonly float $basePrice,
        public readonly ?string $description = null,
        public readonly ?int $categoryId = null,
        public readonly ?float $costPrice = null,
        public readonly string $unit = 'unit',
        public readonly bool $isActive = true,
        public readonly bool $requiresApproval = false,
        /** @var array<string, mixed>|null */
        public readonly ?array $specifications = null,
    ) {}
}
