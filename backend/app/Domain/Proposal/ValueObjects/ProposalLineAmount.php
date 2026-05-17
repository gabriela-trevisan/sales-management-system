<?php

namespace App\Domain\Proposal\ValueObjects;

final readonly class ProposalLineAmount
{
    public function __construct(
        public float $subtotal,
        public float $discountAmount,
        public float $total,
    ) {}

    /**
     * @param array{quantity: int|float, unit_price: float|int|string, discount_percentage?: float|int|string|null} $line
     */
    public static function fromLine(array $line): self
    {
        $quantity = (int) $line['quantity'];
        $unitPrice = (float) $line['unit_price'];
        $discountPercentage = (float) ($line['discount_percentage'] ?? 0);

        if ($quantity <= 0) {
            throw new \InvalidArgumentException('A quantidade do item deve ser maior que zero.');
        }

        if ($unitPrice < 0) {
            throw new \InvalidArgumentException('O preço unitário não pode ser negativo.');
        }

        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new \InvalidArgumentException('O desconto percentual deve estar entre 0 e 100.');
        }

        $subtotal = $quantity * $unitPrice;
        $discountAmount = $subtotal * ($discountPercentage / 100);

        return new self(
            subtotal: $subtotal,
            discountAmount: $discountAmount,
            total: $subtotal - $discountAmount,
        );
    }
}
