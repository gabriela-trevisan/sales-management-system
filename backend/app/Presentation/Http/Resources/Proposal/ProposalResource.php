<?php

namespace App\Presentation\Http\Resources\Proposal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Proposal Resource
 * 
 * Transforma um Proposal model em uma representação JSON.
 * 
 * @property \App\Domain\Proposal\Models\Proposal $resource
 */
class ProposalResource extends JsonResource
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
            'number' => $this->number,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', fn() => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'document' => $this->customer->document,
                'email' => $this->customer->email,
            ]),
            'opportunity_id' => $this->opportunity_id,
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'expiration_date' => $this->expiration_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'total' => (float) $this->total,
            'created_by' => $this->created_by,
            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ]),
            'items' => $this->whenLoaded('items', fn() => ProposalItemResource::collection($this->items)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
            
            // Helper fields
            'is_expired' => $this->isExpired(),
            'can_be_edited' => $this->canBeEdited(),
        ];
    }
}
