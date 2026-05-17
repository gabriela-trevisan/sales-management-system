<?php

namespace App\Domain\Customer\Policies;

use App\Domain\Customer\Models\Customer;
use App\Models\User;

/**
 * CustomerPolicy
 *
 * Centraliza as regras de autorização sobre o recurso Customer.
 * Registrada explicitamente em AppServiceProvider::boot() via Gate::policy(),
 * pois o Model está em um namespace não-padrão (Domain layer).
 *
 * Modelo de acesso: CRM compartilhado por equipe.
 * Qualquer membro autenticado pode gerenciar qualquer registro.
 * O campo assigned_to rastreia responsabilidade (workflow/comissões),
 * mas não restringe acesso — isso seria padrão multi-tenant, não intra-equipe.
 *
 * OWASP A01:2021 – Broken Access Control:
 * Proteção aplicada pelo middleware auth:sanctum (token válido obrigatório).
 * Isolamento multi-tenant seria necessário apenas em SaaS com múltiplas empresas.
 */
class CustomerPolicy
{
    /**
     * Qualquer usuário autenticado pode criar clientes.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Qualquer usuário autenticado pode listar clientes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Qualquer usuário autenticado pode visualizar qualquer cliente.
     */
    public function view(User $user, Customer $customer): bool
    {
        return true;
    }

    /**
     * Qualquer membro da equipe pode atualizar qualquer cliente.
     * O campo assigned_to indica o responsável, não restringe edição.
     */
    public function update(User $user, Customer $customer): bool
    {
        return true;
    }

    /**
     * Qualquer membro da equipe pode remover qualquer cliente.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return true;
    }
}
