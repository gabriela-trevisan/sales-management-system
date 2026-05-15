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
 * OWASP A01:2021 – Broken Access Control:
 * Garante que apenas o vendedor responsável (assigned_to) pode
 * visualizar, atualizar ou excluir seus próprios clientes.
 */
class CustomerPolicy
{
    /**
     * Qualquer usuário autenticado pode listar clientes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Apenas o vendedor responsável pode visualizar o cliente.
     */
    public function view(User $user, Customer $customer): bool
    {
        return $user->id === $customer->assigned_to;
    }

    /**
     * Apenas o vendedor responsável pode atualizar o cliente.
     */
    public function update(User $user, Customer $customer): bool
    {
        return $user->id === $customer->assigned_to;
    }

    /**
     * Apenas o vendedor responsável pode remover o cliente.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $user->id === $customer->assigned_to;
    }
}
