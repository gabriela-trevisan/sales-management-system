<?php

namespace App\Domain\Proposal\Models;

use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProposalItem Model
 * 
 * Representa um item/linha de uma proposta comercial.
 * Cada item possui produto, quantidade, preço e desconto.
 * 
 * @property int $id
 * @property int $proposal_id ID da proposta
 * @property int $product_id ID do produto
 * @property string|null $description Descrição personalizada (sobrescreve a do produto)
 * @property int $quantity Quantidade do item
 * @property float $unit_price Preço unitário no momento da proposta
 * @property float $discount_percentage Percentual de desconto (0-100)
 * @property float $total Total do item (quantity * unit_price * (1 - discount/100))
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Proposal $proposal
 * @property-read Product $product
 */
class ProposalItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'proposal_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'proposal_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount_percentage',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the proposal that owns the item.
     *
     * @return BelongsTo<Proposal, ProposalItem>
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the product associated with the item.
     *
     * @return BelongsTo<Product, ProposalItem>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate and set the total for this item.
     * Total = quantity * unit_price * (1 - discount_percentage/100)
     *
     * @return void
     */
    public function calculateTotal(): void
    {
        $subtotal = $this->quantity * $this->unit_price;
        $discountAmount = $subtotal * ($this->discount_percentage / 100);
        $this->total = $subtotal - $discountAmount;
    }

    /**
     * Get the discount amount for this item.
     *
     * @return float
     */
    public function getDiscountAmount(): float
    {
        $subtotal = $this->quantity * $this->unit_price;
        return $subtotal * ($this->discount_percentage / 100);
    }

    /**
     * Get the subtotal before discount for this item.
     *
     * @return float
     */
    public function getSubtotal(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
