<?php

namespace App\Domain\Proposal\Contracts;

/**
 * Proposal Repository Interface
 * 
 * Define o contrato para operações de persistência de propostas.
 * Segue o padrão Repository do Domain-Driven Design (DDD).
 */
interface ProposalRepositoryInterface
{
    /**
     * Get all proposals with optional filters and pagination.
     *
     * @param array<string, mixed> $filters Filtros disponíveis: status, customer_id, search, per_page
     * @param int $perPage Items per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15);

    /**
     * Find a proposal by ID with relationships.
     *
     * @param int $id
     * @return \App\Domain\Proposal\Models\Proposal|null
     */
    public function findById(int $id);

    /**
     * Create a new proposal with items.
     *
     * @param array<string, mixed> $data
     * @return \App\Domain\Proposal\Models\Proposal
     */
    public function create(array $data);

    /**
     * Update an existing proposal.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return \App\Domain\Proposal\Models\Proposal
     */
    public function update(int $id, array $data);

    /**
     * Delete a proposal (soft delete).
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Generate a unique proposal number.
     *
     * @return string
     */
    public function generateProposalNumber(): string;
}
