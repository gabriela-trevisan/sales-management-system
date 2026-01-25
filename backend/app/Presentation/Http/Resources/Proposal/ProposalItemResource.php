<?php

namespace App\Presentation\Http\Resources\Proposal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Proposal Item Resource
 * 
 * Transforma um ProposalItem model em uma representação JSON.
 */
class ProposalItemResource extends JsonResource
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
            'proposal_id' => $this->proposal_id,
            'product_id' => $this->product_id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
                'category' => $this->whenLoaded('product', function () {
                    return $this->product->category ? [
                        'id' => $this->product->category->id,
                        'name' => $this->product->category->name,
                    ] : null;
                }),
            ],
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'discount_percentage' => (float) $this->discount_percentage,
            'total' => (float) $this->total,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
