<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Proposal\Contracts\ProposalRepositoryInterface;
use App\Domain\Proposal\Models\Proposal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Eloquent Proposal Repository Implementation
 * 
 * Implementa as operações de persistência de propostas usando Eloquent ORM.
 */
class EloquentProposalRepository implements ProposalRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Proposal::with(['customer', 'creator', 'items.product']);

        // Filtro por status
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Filtro por cliente
        if (!empty($filters['customer_id'])) {
            $query->byCustomer($filters['customer_id']);
        }

        // Search por número, nome do cliente ou notas
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('number', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Ordenação por data de emissão (mais recentes primeiro)
        $query->orderBy('issue_date', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?Proposal
    {
        return Proposal::with(['customer', 'creator', 'items.product.category'])
            ->find($id);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Proposal
    {
        // Gera número da proposta se não fornecido
        if (empty($data['number'])) {
            $data['number'] = $this->generateProposalNumber();
        }

        // Extrai items do array principal
        $items = $data['items'] ?? [];
        unset($data['items']);

        // Calcula totais
        $subtotal = 0;
        $totalDiscount = 0;

        foreach ($items as $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $itemSubtotal * ($item['discount_percentage'] ?? 0) / 100;
            
            $subtotal += $itemSubtotal;
            $totalDiscount += $itemDiscount;
        }

        $data['subtotal'] = $subtotal;
        $data['discount'] = $totalDiscount;
        $data['total'] = $subtotal - $totalDiscount;

        // Cria a proposta
        $proposal = Proposal::create($data);

        // Cria os items
        foreach ($items as $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $itemSubtotal * ($item['discount_percentage'] ?? 0) / 100;
            
            $proposal->items()->create([
                'product_id' => $item['product_id'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'total' => $itemSubtotal - $itemDiscount,
            ]);
        }

        return $proposal->load(['customer', 'creator', 'items.product']);
    }

    /**
     * @inheritDoc
     */
    public function update(int $id, array $data): Proposal
    {
        $proposal = Proposal::findOrFail($id);

        // Extrai items do array principal
        $items = $data['items'] ?? null;
        unset($data['items']);

        // Se items foram fornecidos, recalcula totais
        if ($items !== null) {
            $subtotal = 0;
            $totalDiscount = 0;

            foreach ($items as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemDiscount = $itemSubtotal * ($item['discount_percentage'] ?? 0) / 100;
                
                $subtotal += $itemSubtotal;
                $totalDiscount += $itemDiscount;
            }

            $data['subtotal'] = $subtotal;
            $data['discount'] = $totalDiscount;
            $data['total'] = $subtotal - $totalDiscount;

            // Remove items antigos e cria novos
            $proposal->items()->delete();
            
            foreach ($items as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemDiscount = $itemSubtotal * ($item['discount_percentage'] ?? 0) / 100;
                
                $proposal->items()->create([
                    'product_id' => $item['product_id'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'total' => $itemSubtotal - $itemDiscount,
                ]);
            }
        }

        // Atualiza a proposta
        $proposal->update($data);

        return $proposal->load(['customer', 'creator', 'items.product']);
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): bool
    {
        $proposal = Proposal::findOrFail($id);
        return $proposal->delete();
    }

    /**
     * @inheritDoc
     */
    public function generateProposalNumber(): string
    {
        $year = date('Y');
        $lastProposal = Proposal::withTrashed()
            ->where('number', 'LIKE', "PROP-{$year}-%")
            ->orderBy('number', 'desc')
            ->first();

        if ($lastProposal) {
            // Extrai o número sequencial do último número
            preg_match('/PROP-\d{4}-(\d+)/', $lastProposal->number, $matches);
            $sequence = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $sequence = 1;
        }

        return sprintf('PROP-%s-%04d', $year, $sequence);
    }
}
