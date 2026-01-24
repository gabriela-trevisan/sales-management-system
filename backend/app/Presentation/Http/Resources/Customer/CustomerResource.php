<?php

namespace App\Presentation\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'document' => $this->document,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'rfm_score' => $this->rfm_score,
            'segment' => $this->whenLoaded('segment', fn() => [
                'id' => $this->segment->id,
                'name' => $this->segment->name,
            ]),
            'assigned_to' => $this->whenLoaded('assignedUser', fn() => [
                'id' => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
            ]),
            'addresses' => $this->whenLoaded('addresses', fn() => $this->addresses),
            'contacts' => $this->whenLoaded('contacts', fn() => $this->contacts),
            'opportunities' => $this->whenLoaded('opportunities', fn() => $this->opportunities),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
