<?php

namespace Tests\Unit\Domain;

use App\Domain\Proposal\Models\Proposal;
use PHPUnit\Framework\TestCase;

class ProposalAggregateTotalsTest extends TestCase
{
    public function test_aggregate_totals_from_multiple_lines(): void
    {
        $totals = Proposal::aggregateTotalsFromLines([
            [
                'product_id' => 1,
                'quantity' => 10,
                'unit_price' => 250.00,
                'discount_percentage' => 10,
            ],
            [
                'product_id' => 2,
                'quantity' => 5,
                'unit_price' => 100.00,
                'discount_percentage' => 0,
            ],
        ]);

        $this->assertSame(3000.0, $totals['subtotal']);
        $this->assertSame(250.0, $totals['discount']);
        $this->assertSame(2750.0, $totals['total']);
    }
}
