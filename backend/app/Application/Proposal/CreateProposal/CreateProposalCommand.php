<?php

namespace App\Application\Proposal\CreateProposal;

/**
 * Create Proposal Command
 * 
 * Command object que encapsula todos os dados necessários
 * para criar uma nova proposta comercial.
 * Segue o padrão CQRS (Command Query Responsibility Segregation).
 */
class CreateProposalCommand
{
    /**
     * @param int $customerId ID do cliente
     * @param int|null $opportunityId ID da oportunidade (opcional)
     * @param string $issueDate Data de emissão (Y-m-d)
     * @param string $expirationDate Data de validade (Y-m-d)
     * @param string|null $notes Observações adicionais
     * @param string $status Status inicial da proposta
     * @param int $createdBy ID do usuário criador
     * @param array<int, array{product_id: int, description: string|null, quantity: int, unit_price: float, discount_percentage: float}> $items Items da proposta
     */
    public function __construct(
        public readonly int $customerId,
        public readonly ?int $opportunityId,
        public readonly string $issueDate,
        public readonly string $expirationDate,
        public readonly ?string $notes,
        public readonly string $status,
        public readonly int $createdBy,
        public readonly array $items,
    ) {
    }

    /**
     * Converte o command para array.
     * 
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'opportunity_id' => $this->opportunityId,
            'issue_date' => $this->issueDate,
            'expiration_date' => $this->expirationDate,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_by' => $this->createdBy,
            'items' => $this->items,
        ];
    }
}
