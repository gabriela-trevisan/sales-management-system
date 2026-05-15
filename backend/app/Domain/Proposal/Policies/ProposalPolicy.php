<?php

namespace App\Domain\Proposal\Policies;

use App\Domain\Proposal\Models\Proposal;
use App\Models\User;

/**
 * ProposalPolicy
 *
 * Centraliza as regras de autorização sobre o recurso Proposal.
 * Registrada explicitamente em AppServiceProvider::boot() via Gate::policy().
 *
 * OWASP A01:2021 – Broken Access Control:
 * Garante que apenas o criador da proposta (created_by) pode
 * visualizar, atualizar ou excluir suas próprias propostas.
 */
class ProposalPolicy
{
    /**
     * Qualquer usuário autenticado pode listar propostas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Qualquer usuário autenticado pode visualizar a proposta.
     * Restrição de escrita (update/delete) permanece exclusiva do criador.
     */
    public function view(User $user, Proposal $proposal): bool
    {
        return true;
    }

    /**
     * Apenas o criador pode atualizar a proposta.
     */
    public function update(User $user, Proposal $proposal): bool
    {
        return $user->id === $proposal->created_by;
    }

    /**
     * Apenas o criador pode excluir a proposta.
     */
    public function delete(User $user, Proposal $proposal): bool
    {
        return $user->id === $proposal->created_by;
    }
}
