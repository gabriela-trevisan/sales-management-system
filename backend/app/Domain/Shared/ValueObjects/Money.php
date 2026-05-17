<?php

namespace App\Domain\Shared\ValueObjects;

final readonly class Money
{
    public function __construct(public float $amount)
    {
        if ($amount < 0) {
            throw new \App\Domain\Shared\Exceptions\DomainArgumentException('Valor monetário não pode ser negativo.');
        }
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount);
    }

    public function subtract(self $other): self
    {
        $result = $this->amount - $other->amount;

        if ($result < 0) {
            throw new \App\Domain\Shared\Exceptions\DomainArgumentException('Resultado monetário não pode ser negativo.');
        }

        return new self($result);
    }

    public function applyDiscountPercentage(float $percentage): self
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new \App\Domain\Shared\Exceptions\DomainArgumentException('Desconto percentual deve estar entre 0 e 100.');
        }

        return new self($this->amount * (1 - $percentage / 100));
    }

    public function discountAmount(float $percentage): self
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new \App\Domain\Shared\Exceptions\DomainArgumentException('Desconto percentual deve estar entre 0 e 100.');
        }

        return new self($this->amount * ($percentage / 100));
    }

    public function toFloat(): float
    {
        return round($this->amount, 2);
    }
}
