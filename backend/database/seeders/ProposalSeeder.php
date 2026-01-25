<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca opportunities se a tabela existir e tiver dados
        $opportunities = DB::table('opportunities')->exists() ? DB::table('opportunities')->get() : collect();
        $customers = DB::table('customers')->get();
        $users = User::all();
        $products = DB::table('products')->get();

        // Garante que temos dados suficientes
        if ($customers->count() < 3 || $products->count() < 3) {
            $this->command->error('É necessário ter pelo menos 3 clientes e 3 produtos para popular as propostas.');
            return;
        }

        // Usa índices válidos baseados no que realmente temos
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
                    ['product_id' => $products[min(1, $maxProductIndex)]->id, 'description' => null, 'quantity' => 40, 'unit_price' => 250.00, 'discount' => 10],
                    ['product_id' => $products[min(2, $maxProductIndex)]->id, 'description' => null, 'quantity' => 80, 'unit_price' => 180.00, 'discount' => 10],
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
                    ['product_id' => $products[0]->id, 'description' => null, 'quantity' => 1, 'unit_price' => 80000.00, 'discount' => 5],
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
                    ['product_id' => $products[0]->id, 'description' => 'Consultoria especializada', 'quantity' => 80, 'unit_price' => 350.00, 'discount' => 0],
                ],
            ],
        ];

        // Adiciona mais propostas se tivermos clientes e produtos suficientes
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
                    ['product_id' => $products[3]->id, 'description' => null, 'quantity' => 6, 'unit_price' => 35000.00, 'discount' => 8],
                    ['product_id' => $products[4]->id, 'description' => null, 'quantity' => 40, 'unit_price' => 220.00, 'discount' => 0],
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
                    ['product_id' => $products[min(1, $maxProductIndex)]->id, 'description' => null, 'quantity' => 120, 'unit_price' => 250.00, 'discount' => 12],
                ],
            ];
        }

        foreach ($proposals as $proposalData) {
            $subtotal = 0;
            $totalDiscount = 0;

            // Calcular totais
            foreach ($proposalData['items'] as $item) {
                $itemSubtotal = $item['unit_price'] * $item['quantity'];
                $itemDiscount = $itemSubtotal * ($item['discount'] / 100);
                $subtotal += $itemSubtotal;
                $totalDiscount += $itemDiscount;
            }

            $total = $subtotal - $totalDiscount;

            $proposalId = DB::table('proposals')->insertGetId([
                'number' => $proposalData['number'],
                'opportunity_id' => $proposalData['opportunity_id'],
                'customer_id' => $proposalData['customer_id'],
                'issue_date' => $proposalData['issue_date'],
                'expiration_date' => $proposalData['expiration_date'],
                'notes' => $proposalData['notes'],
                'status' => $proposalData['status'],
                'subtotal' => $subtotal,
                'discount' => $totalDiscount,
                'total' => $total,
                'created_by' => $proposalData['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Adicionar itens da proposta
            foreach ($proposalData['items'] as $item) {
                $itemTotal = ($item['unit_price'] * $item['quantity']) * (1 - $item['discount'] / 100);

                DB::table('proposal_items')->insert([
                    'proposal_id' => $proposalId,
                    'product_id' => $item['product_id'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percentage' => $item['discount'],
                    'total' => $itemTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
