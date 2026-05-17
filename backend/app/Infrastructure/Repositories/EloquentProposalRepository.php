<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Proposal\Contracts\ProposalRepositoryInterface;
use App\Domain\Proposal\Models\Proposal;
use App\Domain\Shared\Events\DomainEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class EloquentProposalRepository implements ProposalRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Proposal::with(['customer', 'creator', 'items.product']);

        if (! empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (! empty($filters['customer_id'])) {
            $query->byCustomer($filters['customer_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('number', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $query->orderBy('issue_date', 'desc');

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Proposal
    {
        return Proposal::with(['customer', 'creator', 'items.product.category'])
            ->find($id);
    }

    public function create(array $data): Proposal
    {
        if (empty($data['number'])) {
            $data['number'] = $this->generateProposalNumber();
        }

        $items = $data['items'] ?? [];
        unset($data['items']);

        return DB::transaction(function () use ($data, $items) {
            $proposal = Proposal::create(array_merge(
                $data,
                Proposal::aggregateTotalsFromLines($items)
            ));
            $proposal->attachItems($items);

            $this->dispatchDomainEvents($proposal);

            return $proposal->load(['customer', 'creator', 'items.product']);
        });
    }

    public function update(int $id, array $data): Proposal
    {
        return DB::transaction(function () use ($id, $data) {
            $proposal = Proposal::findOrFail($id);

            $items = $data['items'] ?? null;
            unset($data['items']);

            if ($items !== null) {
                $proposal->replaceItems($items);
            }

            if ($data !== []) {
                $proposal->updateHeader($data);
            }

            $proposal->save();

            $this->dispatchDomainEvents($proposal);

            return $proposal->load(['customer', 'creator', 'items.product']);
        });
    }

    public function delete(int $id): bool
    {
        $proposal = Proposal::findOrFail($id);

        return $proposal->delete();
    }

    public function generateProposalNumber(): string
    {
        $year = date('Y');
        $lastProposal = Proposal::withTrashed()
            ->where('number', 'LIKE', "PROP-{$year}-%")
            ->orderBy('number', 'desc')
            ->first();

        if ($lastProposal) {
            preg_match('/PROP-\d{4}-(\d+)/', $lastProposal->number, $matches);
            $sequence = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $sequence = 1;
        }

        return sprintf('PROP-%s-%04d', $year, $sequence);
    }

    private function dispatchDomainEvents(Proposal $proposal): void
    {
        foreach ($proposal->releaseDomainEvents() as $event) {
            if ($event instanceof DomainEvent) {
                Event::dispatch($event);
            }
        }
    }
}
