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
 * Modelo de acesso: CRM compartilhado por equipe.
 * Qualquer membro autenticado pode gerenciar qualquer proposta.
 * O campo created_by rastreia o criador para histórico e comissões,
 * mas não restringe acesso pós-criação.
 *
 * OWASP A01:2021 – Broken Access Control:
 * Proteção aplicada pelo middleware auth:sanctum (token válido obrigatório).
 */
class ProposalPolicy
{
    /**
     * Qualquer usuário autenticado pode criar propostas.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Qualquer usuário autenticado pode listar propostas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Qualquer usuário autenticado pode visualizar qualquer proposta.
     */
    public function view(User $user, Proposal $proposal): bool
    {
        return true;
    }

    /**
     * Qualquer membro da equipe pode atualizar qualquer proposta.
     * O campo created_by registra o criador, não restringe edição.
     */
    public function update(User $user, Proposal $proposal): bool
    {
        return true;
    }

    /**
     * Qualquer membro da equipe pode excluir qualquer proposta.
     */
    public function delete(User $user, Proposal $proposal): bool
    {
        return true;
    }
}
