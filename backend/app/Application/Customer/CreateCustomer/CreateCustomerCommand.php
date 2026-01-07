<?php

namespace App\Application\Customer\CreateCustomer;

class CreateCustomerCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $document,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly string $status,
        public readonly ?int $assignedTo = null,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'document' => $this->document,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'assigned_to' => $this->assignedTo,
        ];
    }
}
