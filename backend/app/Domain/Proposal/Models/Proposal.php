<?php

namespace App\Domain\Proposal\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\Proposal\Enums\ProposalStatus;
use App\Domain\Proposal\Events\ProposalStatusChanged;
use App\Domain\Proposal\ValueObjects\ProposalLineAmount;
use App\Domain\Shared\Events\RecordsDomainEvents;
use App\Domain\Shared\Exceptions\InvalidDomainStateException;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * Aggregate root: proposta comercial e seus itens.
 */
class Proposal extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait, RecordsDomainEvents;

    protected $table = 'proposals';

    protected $fillable = [
        'number',
        'opportunity_id',
        'customer_id',
        'issue_date',
        'expiration_date',
        'notes',
        'status',
        'subtotal',
        'discount',
        'total',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProposalItem::class);
    }

    public function proposalStatus(): ProposalStatus
    {
        return ProposalStatus::from($this->status);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeActive($query)
    {
        return $query->where('expiration_date', '>=', now())
            ->whereIn('status', [ProposalStatus::Draft->value, ProposalStatus::Sent->value]);
    }

    public function isExpired(): bool
    {
        return $this->expiration_date < now()
            && $this->proposalStatus() !== ProposalStatus::Approved;
    }

    public function canBeEdited(): bool
    {
        return $this->proposalStatus()->isEditable();
    }

    /**
     * @param array<int, array{product_id: int, description?: string|null, quantity: int, unit_price: float, discount_percentage?: float}> $lines
     * @return array{subtotal: float, discount: float, total: float}
     */
    public static function aggregateTotalsFromLines(array $lines): array
    {
        if ($lines === []) {
            throw new InvalidDomainStateException('A proposta deve conter ao menos um item.');
        }

        $subtotal = 0.0;
        $totalDiscount = 0.0;

        foreach ($lines as $line) {
            $amounts = ProposalLineAmount::fromLine($line);
            $subtotal += $amounts->subtotal;
            $totalDiscount += $amounts->discountAmount;
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $totalDiscount,
            'total' => $subtotal - $totalDiscount,
        ];
    }

    /**
     * Persiste itens no agregado (requer proposta já salva).
     *
     * @param array<int, array{product_id: int, description?: string|null, quantity: int, unit_price: float, discount_percentage?: float}> $lines
     */
    public function attachItems(array $lines): void
    {
        foreach ($lines as $line) {
            $amounts = ProposalLineAmount::fromLine($line);
            $this->items()->create(ProposalItem::attributesFromLine($line, $amounts));
        }
    }

    /**
     * Substitui todos os itens e recalcula totais do agregado.
     *
     * @param array<int, array{product_id: int, description?: string|null, quantity: int, unit_price: float, discount_percentage?: float}> $lines
     */
    public function replaceItems(array $lines): void
    {
        $this->ensureEditable();

        $this->fill(self::aggregateTotalsFromLines($lines));
        $this->items()->delete();
        $this->attachItems($lines);
    }

    public function send(): void
    {
        $this->applyStatusTransition(ProposalStatus::Sent);
    }

    public function approve(): void
    {
        $this->applyStatusTransition(ProposalStatus::Approved);
    }

    public function reject(): void
    {
        $this->applyStatusTransition(ProposalStatus::Rejected);
    }

    public function markAsExpired(): void
    {
        $this->applyStatusTransition(ProposalStatus::Expired);
    }

    /**
     * Atualiza campos do cabeçalho (sem itens). Itens usam replaceItems().
     *
     * @param array<string, mixed> $headerData
     */
    public function updateHeader(array $headerData): void
    {
        $this->ensureEditable();

        if (isset($headerData['status'])) {
            $this->applyStatusTransition(ProposalStatus::from($headerData['status']));
            unset($headerData['status']);
        }

        $this->fill($headerData);
    }

    private function applyStatusTransition(ProposalStatus $next): void
    {
        $current = $this->proposalStatus();

        if (! $current->canTransitionTo($next)) {
            throw new InvalidDomainStateException(
                "Não é possível alterar o status de '{$current->value}' para '{$next->value}'."
            );
        }

        if ($current === $next) {
            return;
        }

        $this->status = $next->value;

        if ($this->exists) {
            $this->recordDomainEvent(new ProposalStatusChanged(
                proposalId: (int) $this->id,
                from: $current,
                to: $next,
            ));
        }
    }

    private function ensureEditable(): void
    {
        if ($this->exists && ! $this->canBeEdited()) {
            throw new InvalidDomainStateException(
                'Propostas aprovadas, rejeitadas ou expiradas não podem ser alteradas.'
            );
        }
    }
}
