<?php

namespace App\Presentation\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'document' => $this->document,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'rfm_score' => $this->rfm_score,
            'assigned_to' => [
                'id' => $this->assignedUser?->id,
                'name' => $this->assignedUser?->name,
            ],
            'addresses_count' => $this->whenLoaded('addresses', fn() => $this->addresses->count()),
            'contacts_count' => $this->whenLoaded('contacts', fn() => $this->contacts->count()),
            'opportunities_count' => $this->whenLoaded('opportunities', fn() => $this->opportunities->count()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
