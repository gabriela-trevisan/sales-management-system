<?php

namespace Tests\Unit\Domain;

use App\Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_apply_discount_percentage(): void
    {
        $price = new Money(1000.0);
        $discounted = $price->applyDiscountPercentage(10);

        $this->assertSame(900.0, $discounted->toFloat());
    }
}
