<?php

namespace App\Domain\Proposal\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\Product\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * Proposal Model
 * 
 * Representa uma proposta comercial enviada ao cliente.
 * Pode ou não estar vinculada a uma oportunidade (opportunity_id nullable).
 * 
 * @property int $id
 * @property string $number Número único da proposta
 * @property int|null $opportunity_id ID da oportunidade (opcional)
 * @property int $customer_id ID do cliente
 * @property \Carbon\Carbon $issue_date Data de emissão
 * @property \Carbon\Carbon $expiration_date Data de validade
 * @property string|null $notes Observações/notas adicionais
 * @property string $status Status da proposta (draft, sent, approved, rejected, expired)
 * @property float $subtotal Subtotal antes dos descontos
 * @property float $discount Valor total de desconto
 * @property float $total Valor total final
 * @property int $created_by ID do usuário que criou
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * 
 * @property-read Customer $customer
 * @property-read User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<ProposalItem> $items
 */
class Proposal extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The table associated with the model.
     */
    protected $table = 'proposals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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

    /**
     * Get the customer that owns the proposal.
     *
     * @return BelongsTo<Customer, Proposal>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who created the proposal.
     *
     * @return BelongsTo<User, Proposal>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the items for the proposal.
     *
     * @return HasMany<ProposalItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ProposalItem::class);
    }

    /**
     * Scope a query to only include proposals with a specific status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include proposals for a specific customer.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $customerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to only include active (not expired) proposals.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('expiration_date', '>=', now())
                     ->whereIn('status', ['draft', 'sent']);
    }

    /**
     * Check if the proposal is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expiration_date < now() && $this->status !== 'approved';
    }

    /**
     * Check if the proposal can be edited.
     *
     * @return bool
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'sent']);
    }
}
