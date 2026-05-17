<?php

namespace App\Domain\Proposal\Models;

use App\Domain\Product\Models\Product;
use App\Domain\Proposal\ValueObjects\ProposalLineAmount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalItem extends Model
{
    use HasFactory;

    protected $table = 'proposal_items';

    protected $fillable = [
        'proposal_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount_percentage',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param array{product_id: int, description?: string|null, quantity: int, unit_price: float, discount_percentage?: float} $line
     * @return array<string, mixed>
     */
    public static function attributesFromLine(array $line, ?ProposalLineAmount $amounts = null): array
    {
        $amounts ??= ProposalLineAmount::fromLine($line);

        return [
            'product_id' => $line['product_id'],
            'description' => $line['description'] ?? null,
            'quantity' => (int) $line['quantity'],
            'unit_price' => $line['unit_price'],
            'discount_percentage' => $line['discount_percentage'] ?? 0,
            'total' => $amounts->total,
        ];
    }

    public function lineAmount(): ProposalLineAmount
    {
        return ProposalLineAmount::fromLine([
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'discount_percentage' => (float) $this->discount_percentage,
        ]);
    }

    public function getDiscountAmount(): float
    {
        return $this->lineAmount()->discountAmount;
    }

    public function getSubtotal(): float
    {
        return $this->lineAmount()->subtotal;
    }

    /**
     * @deprecated Use lineAmount() ou attributesFromLine() no agregado Proposal.
     */
    public function calculateTotal(): void
    {
        $this->total = $this->lineAmount()->total;
    }
}
