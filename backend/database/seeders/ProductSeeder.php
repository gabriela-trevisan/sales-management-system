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
            // Desenvolvimento
            [
                'name' => 'Hora Arquiteto de Software',
                'sku' => 'DEV-ARCH',
                'description' => 'Profissional sênior especialista em arquitetura de soluções',
                'category_id' => 3,
                'base_price' => 350.00,
                'cost_price' => 200.00,
                'unit' => 'hour',
            ],
            [
                'name' => 'Hora Desenvolvedor Sênior',
                'sku' => 'DEV-SR',
                'description' => 'Desenvolvedor com +8 anos de experiência',
                'category_id' => 3,
                'base_price' => 250.00,
                'cost_price' => 150.00,
                'unit' => 'hour',
            ],
            [
                'name' => 'Hora Desenvolvedor Pleno',
                'sku' => 'DEV-PL',
                'description' => 'Desenvolvedor com 4-7 anos de experiência',
                'category_id' => 3,
                'base_price' => 180.00,
                'cost_price' => 110.00,
                'unit' => 'hour',
            ],
            [
                'name' => 'Hora Desenvolvedor Júnior',
                'sku' => 'DEV-JR',
                'description' => 'Desenvolvedor com até 3 anos de experiência',
                'category_id' => 3,
                'base_price' => 120.00,
                'cost_price' => 75.00,
                'unit' => 'hour',
            ],
            // Qualidade e Metodologia
            [
                'name' => 'Hora QA/Tester',
                'sku' => 'QA-001',
                'description' => 'Analista de qualidade e testes automatizados',
                'category_id' => 3,
                'base_price' => 160.00,
                'cost_price' => 100.00,
                'unit' => 'hour',
            ],
            [
                'name' => 'Hora Scrum Master',
                'sku' => 'SM-001',
                'description' => 'Facilitador ágil e gestão de projetos',
                'category_id' => 3,
                'base_price' => 220.00,
                'cost_price' => 130.00,
                'unit' => 'hour',
            ],
            [
                'name' => 'Hora Product Owner',
                'sku' => 'PO-001',
                'description' => 'Gestão de produto e backlog',
                'category_id' => 3,
                'base_price' => 240.00,
                'cost_price' => 140.00,
                'unit' => 'hour',
            ],
            // DevOps e Infraestrutura
            [
                'name' => 'Hora DevOps Engineer',
                'sku' => 'OPS-001',
                'description' => 'Especialista em CI/CD, cloud e automação',
                'category_id' => 3,
                'base_price' => 280.00,
                'cost_price' => 170.00,
                'unit' => 'hour',
            ],
            // UX/UI
            [
                'name' => 'Hora UX/UI Designer',
                'sku' => 'UX-001',
                'description' => 'Design de interfaces e experiência do usuário',
                'category_id' => 3,
                'base_price' => 200.00,
                'cost_price' => 120.00,
                'unit' => 'hour',
            ],
            // Pacotes de Projeto
            [
                'name' => 'Pacote Discovery (40h)',
                'sku' => 'PKG-DISC',
                'description' => 'Levantamento de requisitos, prototipação e proposta técnica',
                'category_id' => 3,
                'base_price' => 12000.00,
                'cost_price' => 7000.00,
                'unit' => 'unit',
            ],
            [
                'name' => 'Pacote MVP (320h)',
                'sku' => 'PKG-MVP',
                'description' => 'Desenvolvimento de Produto Mínimo Viável',
                'category_id' => 3,
                'base_price' => 80000.00,
                'cost_price' => 50000.00,
                'unit' => 'unit',
            ],
            [
                'name' => 'Pacote Squad Dedicado (160h/mês)',
                'sku' => 'PKG-SQUAD',
                'description' => 'Time dedicado com 4 desenvolvedores + PO + QA',
                'category_id' => 3,
                'base_price' => 35000.00,
                'cost_price' => 22000.00,
                'unit' => 'month',
            ],
            // Licenças e Ferramentas
            [
                'name' => 'Licença Azure DevOps (por usuário)',
                'sku' => 'LIC-ADO',
                'description' => 'Gerenciamento de código, CI/CD e projetos',
                'category_id' => 7,
                'base_price' => 150.00,
                'cost_price' => 100.00,
                'unit' => 'month',
            ],
            [
                'name' => 'Licença JetBrains All Products',
                'sku' => 'LIC-JB',
                'description' => 'Pacote completo de IDEs JetBrains',
                'category_id' => 7,
                'base_price' => 280.00,
                'cost_price' => 200.00,
                'unit' => 'month',
            ],
            // Suporte e Manutenção
            [
                'name' => 'Suporte Evolutivo (40h/mês)',
                'sku' => 'SUP-EVO',
                'description' => 'Manutenção evolutiva e correções pós go-live',
                'category_id' => 4,
                'base_price' => 9000.00,
                'cost_price' => 5500.00,
                'unit' => 'month',
            ],
            [
                'name' => 'Suporte Corretivo 24x7',
                'sku' => 'SUP-247',
                'description' => 'Atendimento emergencial com SLA de 2h',
                'category_id' => 4,
                'base_price' => 15000.00,
                'cost_price' => 9000.00,
                'unit' => 'month',
            ],
        ];

        foreach ($products as $product) {
            if (DB::table('products')->where('sku', $product['sku'])->exists()) {
                continue;
            }

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
