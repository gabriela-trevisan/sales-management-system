<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PipelineStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Prospecção',
                'description' => 'Identificação de potenciais clientes',
                'order' => 1,
                'probability' => 10,
                'color' => '#94a3b8',
            ],
            [
                'name' => 'Qualificação',
                'description' => 'Validação do fit do cliente',
                'order' => 2,
                'probability' => 20,
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Apresentação',
                'description' => 'Demonstração da solução',
                'order' => 3,
                'probability' => 40,
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Proposta',
                'description' => 'Proposta comercial enviada',
                'order' => 4,
                'probability' => 60,
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Negociação',
                'description' => 'Ajustes finais e negociação',
                'order' => 5,
                'probability' => 80,
                'color' => '#eab308',
            ],
            [
                'name' => 'Fechamento',
                'description' => 'Finalização do contrato',
                'order' => 6,
                'probability' => 90,
                'color' => '#22c55e',
            ],
        ];

        foreach ($stages as $stage) {
            DB::table('pipeline_stages')->insert([
                'name' => $stage['name'],
                'description' => $stage['description'],
                'order' => $stage['order'],
                'probability' => $stage['probability'],
                'color' => $stage['color'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
