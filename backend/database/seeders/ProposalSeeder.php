<?php

namespace Database\Seeders;

use App\Domain\Proposal\Models\Proposal;
use App\Domain\Proposal\Models\ProposalItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProposalSeeder extends Seeder
{
    public function run(): void
    {
        $opportunities = DB::table('opportunities')->exists() ? DB::table('opportunities')->get() : collect();
        $customers = DB::table('customers')->get();
        $users = User::all();
        $products = DB::table('products')->get();

        if ($customers->count() < 3 || $products->count() < 3) {
            $this->command->error('É necessário ter pelo menos 3 clientes e 3 produtos para popular as propostas.');

            return;
        }

        $maxProductIndex = $products->count() - 1;
        $maxCustomerIndex = $customers->count() - 1;

        $proposals = [
            [
                'number' => 'PROP-2026-001',
                'opportunity_id' => $opportunities->isNotEmpty() ? $opportunities->first()->id : null,
                'customer_id' => $customers[0]->id,
                'issue_date' => now()->subDays(15)->format('Y-m-d'),
                'expiration_date' => now()->addDays(15)->format('Y-m-d'),
                'notes' => 'Proposta comercial para implementação de sistema customizado com squad dedicado por 3 meses.',
                'status' => 'sent',
                'created_by' => $users->random()->id,
                'items' => [
                    ['product_id' => $products[min(1, $maxProductIndex)]->id, 'description' => null, 'quantity' => 40, 'unit_price' => 250.00, 'discount_percentage' => 10],
                    ['product_id' => $products[min(2, $maxProductIndex)]->id, 'description' => null, 'quantity' => 80, 'unit_price' => 180.00, 'discount_percentage' => 10],
                ],
            ],
            [
                'number' => 'PROP-2026-002',
                'opportunity_id' => $opportunities->count() > 1 ? $opportunities[1]->id : null,
                'customer_id' => $customers[min(1, $maxCustomerIndex)]->id,
                'issue_date' => now()->subDays(10)->format('Y-m-d'),
                'expiration_date' => now()->addDays(20)->format('Y-m-d'),
                'notes' => 'Proposta para desenvolvimento de MVP com suporte evolutivo de 3 meses.',
                'status' => 'approved',
                'created_by' => $users->random()->id,
                'items' => [
                    ['product_id' => $products[0]->id, 'description' => null, 'quantity' => 1, 'unit_price' => 80000.00, 'discount_percentage' => 5],
                ],
            ],
            [
                'number' => 'PROP-2026-003',
                'opportunity_id' => null,
                'customer_id' => $customers[min(2, $maxCustomerIndex)]->id,
                'issue_date' => now()->subDays(5)->format('Y-m-d'),
                'expiration_date' => now()->addDays(25)->format('Y-m-d'),
                'notes' => 'Proposta para consultoria de arquitetura de software com entrega de documentação técnica.',
                'status' => 'sent',
                'created_by' => $users->random()->id,
                'items' => [
                    ['product_id' => $products[0]->id, 'description' => 'Consultoria especializada', 'quantity' => 80, 'unit_price' => 350.00, 'discount_percentage' => 0],
                ],
            ],
        ];

        if ($maxCustomerIndex >= 3 && $maxProductIndex >= 5) {
            $proposals[] = [
                'number' => 'PROP-2026-004',
                'opportunity_id' => null,
                'customer_id' => $customers[3]->id,
                'issue_date' => now()->subDays(3)->format('Y-m-d'),
                'expiration_date' => now()->addDays(27)->format('Y-m-d'),
                'notes' => 'Squad dedicado fullstack para projeto de 6 meses.',
                'status' => 'draft',
                'created_by' => $users->random()->id,
                'items' => [
                    ['product_id' => $products[3]->id, 'description' => null, 'quantity' => 6, 'unit_price' => 35000.00, 'discount_percentage' => 8],
                    ['product_id' => $products[4]->id, 'description' => null, 'quantity' => 40, 'unit_price' => 220.00, 'discount_percentage' => 0],
                ],
            ];
        }

        if ($maxCustomerIndex >= 4) {
            $proposals[] = [
                'number' => 'PROP-2026-005',
                'opportunity_id' => null,
                'customer_id' => $customers[4]->id,
                'issue_date' => now()->subDays(20)->format('Y-m-d'),
                'expiration_date' => now()->subDays(5)->format('Y-m-d'),
                'notes' => 'Proposta para modernização de sistema legado.',
                'status' => 'expired',
                'created_by' => $users->random()->id,
                'items' => [
                    ['product_id' => $products[min(1, $maxProductIndex)]->id, 'description' => null, 'quantity' => 120, 'unit_price' => 250.00, 'discount_percentage' => 12],
                ],
            ];
        }

        foreach ($proposals as $proposalData) {
            if (DB::table('proposals')->where('number', $proposalData['number'])->exists()) {
                continue;
            }

            $items = $proposalData['items'];
            unset($proposalData['items']);

            $totals = Proposal::aggregateTotalsFromLines($items);

            $proposalId = DB::table('proposals')->insertGetId([
                ...$proposalData,
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'total' => $totals['total'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($items as $line) {
                $attributes = ProposalItem::attributesFromLine($line);

                DB::table('proposal_items')->insert([
                    'proposal_id' => $proposalId,
                    ...$attributes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
