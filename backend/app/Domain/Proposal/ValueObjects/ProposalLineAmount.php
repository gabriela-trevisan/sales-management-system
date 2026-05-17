<?php

namespace App\Domain\Proposal\ValueObjects;

use App\Domain\Shared\ValueObjects\Money;

final readonly class ProposalLineAmount
{
    public function __construct(
        public Money $subtotal,
        public Money $discountAmount,
        public Money $total,
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
            throw new \App\Domain\Shared\Exceptions\DomainArgumentException('A quantidade do item deve ser maior que zero.');
        }

        if ($unitPrice < 0) {
            throw new \App\Domain\Shared\Exceptions\DomainArgumentException('O preço unitário não pode ser negativo.');
        }

        $subtotal = new Money($quantity * $unitPrice);
        $discountAmount = $subtotal->discountAmount($discountPercentage);

        return new self(
            subtotal: $subtotal,
            discountAmount: $discountAmount,
            total: $subtotal->subtract($discountAmount),
        );
    }

    public function subtotalAsFloat(): float
    {
        return $this->subtotal->toFloat();
    }

    public function discountAmountAsFloat(): float
    {
        return $this->discountAmount->toFloat();
    }

    public function totalAsFloat(): float
    {
        return $this->total->toFloat();
    }
}
