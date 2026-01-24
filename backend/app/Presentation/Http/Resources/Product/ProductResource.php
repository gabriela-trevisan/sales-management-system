<?php

namespace App\Presentation\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'category' => $this->when($this->resource->relationLoaded('category'), function () {
                return $this->category ? [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ] : null;
            }),
            'base_price' => (float) $this->base_price,
            'cost_price' => $this->cost_price ? (float) $this->cost_price : null,
            'unit' => $this->unit,
            'is_active' => (bool) $this->is_active,
            'requires_approval' => (bool) $this->requires_approval,
            'specifications' => $this->specifications,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
