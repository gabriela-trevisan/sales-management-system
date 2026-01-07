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
        $opportunities = DB::table('opportunities')->get();
        $customers = DB::table('customers')->get();
        $users = User::all();

        $proposals = [
            [
                'number' => 'PROP-2026-001',
                'opportunity_id' => $opportunities[0]->id,
                'customer_id' => $customers[0]->id,
                'issue_date' => now(),
                'expiration_date' => now()->addDays(30),
                'notes' => 'Proposta comercial para implementação do sistema ERP com customizações conforme reunião do dia 02/01/2026.',
                'status' => 'sent',
                'created_by' => $users->random()->id,
                'items' => [
                    ['product_id' => 1, 'quantity' => 12, 'unit_price' => 15000.00, 'discount' => 10],
                    ['product_id' => 7, 'quantity' => 1, 'unit_price' => 12000.00, 'discount' => 0],
                    ['product_id' => 11, 'quantity' => 1, 'unit_price' => 2500.00, 'discount' => 0],
                ],
            ],
            [
                'number' => 'PROP-2026-002',
                'opportunity_id' => $opportunities[1]->id,
                'customer_id' => $customers[1]->id,
                'issue_date' => now()->subDays(5),
                'expiration_date' => now()->addDays(25),
                'notes' => 'Proposta para renovação de infraestrutura com servidores Dell PowerEdge e notebooks corporativos.',
                'status' => 'approved',
                'created_by' => $users->random()->id,
                'items' => [
                    ['product_id' => 4, 'quantity' => 2, 'unit_price' => 45000.00, 'discount' => 5],
                    ['product_id' => 5, 'quantity' => 10, 'unit_price' => 6500.00, 'discount' => 8],
                ],
            ],
            [
                'number' => 'PROP-2026-003',
                'opportunity_id' => $opportunities[4]->id,
                'customer_id' => $customers[4]->id,
                'issue_date' => now()->subDays(2),
                'expiration_date' => now()->addDays(28),
                'notes' => 'Proposta para migração completa para ambiente cloud incluindo backup e hospedagem.',
                'status' => 'sent',
                'created_by' => $users->random()->id,
                'items' => [
                    ['product_id' => 9, 'quantity' => 12, 'unit_price' => 500.00, 'discount' => 5],
                    ['product_id' => 10, 'quantity' => 12, 'unit_price' => 800.00, 'discount' => 5],
                    ['product_id' => 6, 'quantity' => 40, 'unit_price' => 250.00, 'discount' => 0],
                ],
            ],
        ];

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
