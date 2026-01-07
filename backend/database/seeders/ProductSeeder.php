<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Software
            [
                'name' => 'Sistema ERP Completo',
                'sku' => 'ERP-001',
                'description' => 'Sistema de gestão empresarial integrado',
                'category_id' => 1,
                'base_price' => 15000.00,
                'cost_price' => 7500.00,
                'unit' => 'month',
            ],
            [
                'name' => 'CRM Pro',
                'sku' => 'CRM-001',
                'description' => 'Sistema de gestão de relacionamento com cliente',
                'category_id' => 1,
                'base_price' => 8000.00,
                'cost_price' => 4000.00,
                'unit' => 'month',
            ],
            [
                'name' => 'Licença Microsoft 365 Business',
                'sku' => 'MS365-001',
                'description' => 'Pacote completo Microsoft 365',
                'category_id' => 7,
                'base_price' => 120.00,
                'cost_price' => 80.00,
                'unit' => 'month',
            ],
            // Hardware
            [
                'name' => 'Servidor Dell PowerEdge R740',
                'sku' => 'SRV-001',
                'description' => 'Servidor rack 2U com processador Intel Xeon',
                'category_id' => 2,
                'base_price' => 45000.00,
                'cost_price' => 35000.00,
                'unit' => 'unit',
            ],
            [
                'name' => 'Notebook Dell Latitude 5420',
                'sku' => 'NB-001',
                'description' => 'Notebook corporativo 14" Intel Core i7',
                'category_id' => 2,
                'base_price' => 6500.00,
                'cost_price' => 5000.00,
                'unit' => 'unit',
            ],
            // Serviços
            [
                'name' => 'Consultoria em Infraestrutura',
                'sku' => 'CONS-001',
                'description' => 'Consultoria especializada em infraestrutura de TI',
                'category_id' => 3,
                'base_price' => 250.00,
                'cost_price' => 150.00,
                'unit' => 'hour',
            ],
            [
                'name' => 'Implementação de Sistema',
                'sku' => 'IMPL-001',
                'description' => 'Serviço de implantação e customização',
                'category_id' => 3,
                'base_price' => 12000.00,
                'cost_price' => 7000.00,
                'unit' => 'unit',
            ],
            // Suporte
            [
                'name' => 'Suporte Técnico Premium',
                'sku' => 'SUP-001',
                'description' => 'Suporte 24x7 com SLA prioritário',
                'category_id' => 4,
                'base_price' => 3500.00,
                'cost_price' => 1800.00,
                'unit' => 'month',
            ],
            // Cloud
            [
                'name' => 'Cloud Backup Pro',
                'sku' => 'BCK-001',
                'description' => 'Backup em nuvem com 1TB de armazenamento',
                'category_id' => 6,
                'base_price' => 500.00,
                'cost_price' => 200.00,
                'unit' => 'month',
            ],
            [
                'name' => 'Hospedagem Cloud VPS',
                'sku' => 'VPS-001',
                'description' => 'Servidor virtual privado com 8GB RAM',
                'category_id' => 6,
                'base_price' => 800.00,
                'cost_price' => 400.00,
                'unit' => 'month',
            ],
            // Treinamento
            [
                'name' => 'Treinamento ERP Básico',
                'sku' => 'TRN-001',
                'description' => 'Curso de capacitação no sistema ERP (16h)',
                'category_id' => 5,
                'base_price' => 2500.00,
                'cost_price' => 1200.00,
                'unit' => 'unit',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'name' => $product['name'],
                'sku' => $product['sku'],
                'description' => $product['description'],
                'category_id' => $product['category_id'],
                'base_price' => $product['base_price'],
                'cost_price' => $product['cost_price'],
                'unit' => $product['unit'],
                'is_active' => true,
                'requires_approval' => $product['base_price'] > 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
