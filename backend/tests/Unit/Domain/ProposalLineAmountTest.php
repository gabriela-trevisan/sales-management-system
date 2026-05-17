<?php

namespace Tests\Unit\Domain;

use App\Domain\Proposal\ValueObjects\ProposalLineAmount;
use PHPUnit\Framework\TestCase;

class ProposalLineAmountTest extends TestCase
{
    public function test_calculates_line_totals_with_discount(): void
    {
        $amounts = ProposalLineAmount::fromLine([
            'quantity' => 10,
            'unit_price' => 250.00,
            'discount_percentage' => 10,
        ]);

        $this->assertSame(2500.0, $amounts->subtotalAsFloat());
        $this->assertSame(250.0, $amounts->discountAmountAsFloat());
        $this->assertSame(2250.0, $amounts->totalAsFloat());
    }

    public function test_rejects_invalid_discount(): void
    {
        $this->expectException(\App\Domain\Shared\Exceptions\DomainArgumentException::class);

        ProposalLineAmount::fromLine([
            'quantity' => 1,
            'unit_price' => 100,
            'discount_percentage' => 150,
        ]);
    }
}
