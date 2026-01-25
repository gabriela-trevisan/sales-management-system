<?php

namespace App\Application\Proposal\CreateProposal;

use App\Domain\Proposal\Contracts\ProposalRepositoryInterface;
use App\Domain\Proposal\Models\Proposal;

/**
 * Create Proposal Handler
 * 
 * Processa o comando de criação de proposta,
 * delegando a persistência ao repository.
 */
class CreateProposalHandler
{
    /**
     * @param ProposalRepositoryInterface $proposalRepository
     */
    public function __construct(
        private readonly ProposalRepositoryInterface $proposalRepository
    ) {
    }

    /**
     * Handle the create proposal command.
     *
     * @param CreateProposalCommand $command
     * @return Proposal
     */
    public function handle(CreateProposalCommand $command): Proposal
    {
        $data = [
            'customer_id' => $command->customerId,
            'opportunity_id' => $command->opportunityId,
            'issue_date' => $command->issueDate,
            'expiration_date' => $command->expirationDate,
            'notes' => $command->notes,
            'status' => $command->status,
            'created_by' => $command->createdBy,
            'items' => $command->items,
        ];

        return $this->proposalRepository->create($data);
    }
}
