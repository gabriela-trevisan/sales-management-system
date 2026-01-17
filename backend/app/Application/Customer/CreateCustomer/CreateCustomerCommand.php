<?php

namespace App\Application\Customer\CreateCustomer;

/**
 * Command para criação de cliente.
 * 
 * Encapsula os dados necessários para criar um novo cliente no sistema.
 * O campo assignedTo é auto-atribuído ao usuário logado no controller.
 */
class CreateCustomerCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $document,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly string $status,
        public readonly ?int $segmentId = null,
        public readonly ?int $assignedTo = null,
    ) {}

    /**
     * Converte o command para array.
     * 
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'document' => $this->document,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'segment_id' => $this->segmentId,
            'assigned_to' => $this->assignedTo,
        ];
    }
}
