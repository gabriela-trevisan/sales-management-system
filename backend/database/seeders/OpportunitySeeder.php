<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = DB::table('customers')->get();
        $products = DB::table('products')->get();
        $users = User::all();
        $stages = DB::table('pipeline_stages')->get();

        $opportunities = [
            [
                'title' => 'Implementação ERP Completo',
                'description' => 'Projeto de implementação do sistema ERP com customizações',
                'customer_id' => $customers[0]->id,
                'pipeline_stage_id' => $stages[3]->id, // Proposta
                'value' => 95000.00,
                'probability' => 60,
                'expected_close_date' => now()->addDays(15),
                'status' => 'open',
                'assigned_to' => $users->random()->id,
                'products' => [
                    ['product_id' => 1, 'quantity' => 12, 'unit_price' => 15000.00, 'discount' => 10],
                    ['product_id' => 7, 'quantity' => 1, 'unit_price' => 12000.00, 'discount' => 0],
                    ['product_id' => 11, 'quantity' => 1, 'unit_price' => 2500.00, 'discount' => 0],
                ],
            ],
            [
                'title' => 'Atualização de Infraestrutura',
                'description' => 'Renovação completa de servidores e notebooks',
                'customer_id' => $customers[1]->id,
                'pipeline_stage_id' => $stages[4]->id, // Negociação
                'value' => 135000.00,
                'probability' => 80,
                'expected_close_date' => now()->addDays(10),
                'status' => 'open',
                'assigned_to' => $users->random()->id,
                'products' => [
                    ['product_id' => 4, 'quantity' => 2, 'unit_price' => 45000.00, 'discount' => 5],
                    ['product_id' => 5, 'quantity' => 10, 'unit_price' => 6500.00, 'discount' => 8],
                ],
            ],
            [
                'title' => 'Contrato CRM + Suporte',
                'description' => 'Implementação de CRM com contrato de suporte',
                'customer_id' => $customers[2]->id,
                'pipeline_stage_id' => $stages[2]->id, // Apresentação
                'value' => 54000.00,
                'probability' => 40,
                'expected_close_date' => now()->addDays(30),
                'status' => 'open',
                'assigned_to' => $users->random()->id,
                'products' => [
                    ['product_id' => 2, 'quantity' => 12, 'unit_price' => 8000.00, 'discount' => 15],
                    ['product_id' => 8, 'quantity' => 12, 'unit_price' => 3500.00, 'discount' => 10],
                ],
            ],
            [
                'title' => 'Licenças Microsoft 365',
                'description' => 'Contrato anual de licenças Microsoft para toda empresa',
                'customer_id' => $customers[3]->id,
                'pipeline_stage_id' => $stages[1]->id, // Qualificação
                'value' => 7200.00,
                'probability' => 20,
                'expected_close_date' => now()->addDays(45),
                'status' => 'open',
                'assigned_to' => $users->random()->id,
                'products' => [
                    ['product_id' => 3, 'quantity' => 50, 'unit_price' => 120.00, 'discount' => 0],
                ],
            ],
            [
                'title' => 'Serviços Cloud Completo',
                'description' => 'Migração para cloud com backup e hospedagem',
                'customer_id' => $customers[4]->id,
                'pipeline_stage_id' => $stages[5]->id, // Fechamento
                'value' => 28800.00,
                'probability' => 90,
                'expected_close_date' => now()->addDays(5),
                'status' => 'open',
                'assigned_to' => $users->random()->id,
                'products' => [
                    ['product_id' => 9, 'quantity' => 12, 'unit_price' => 500.00, 'discount' => 5],
                    ['product_id' => 10, 'quantity' => 12, 'unit_price' => 800.00, 'discount' => 5],
                    ['product_id' => 6, 'quantity' => 40, 'unit_price' => 250.00, 'discount' => 0],
                ],
            ],
        ];

        foreach ($opportunities as $oppData) {
            $opportunityId = DB::table('opportunities')->insertGetId([
                'title' => $oppData['title'],
                'description' => $oppData['description'],
                'customer_id' => $oppData['customer_id'],
                'pipeline_stage_id' => $oppData['pipeline_stage_id'],
                'value' => $oppData['value'],
                'probability' => $oppData['probability'],
                'expected_close_date' => $oppData['expected_close_date'],
                'status' => $oppData['status'],
                'assigned_to' => $oppData['assigned_to'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Adicionar produtos na oportunidade
            foreach ($oppData['products'] as $product) {
                $unitPrice = $product['unit_price'];
                $quantity = $product['quantity'];
                $discount = $product['discount'];
                $total = ($unitPrice * $quantity) * (1 - $discount / 100);

                DB::table('opportunity_products')->insert([
                    'opportunity_id' => $opportunityId,
                    'product_id' => $product['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => $discount,
                    'total' => $total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
