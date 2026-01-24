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
                'description' => 'Identificação e primeiro contato com potenciais clientes',
                'order' => 1,
                'probability' => 10,
                'color' => '#94a3b8',
            ],
            [
                'name' => 'Discovery',
                'description' => 'Reuniões de descoberta e entendimento da necessidade',
                'order' => 2,
                'probability' => 25,
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Proposta Técnica',
                'description' => 'Elaboração e apresentação da proposta técnica',
                'order' => 3,
                'probability' => 50,
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Negociação Comercial',
                'description' => 'Negociação de valores, prazos e condições',
                'order' => 4,
                'probability' => 75,
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Contrato',
                'description' => 'Elaboração e assinatura de contrato',
                'order' => 5,
                'probability' => 90,
                'color' => '#eab308',
            ],
            [
                'name' => 'Ganho',
                'description' => 'Projeto fechado e aguardando kick-off',
                'order' => 6,
                'probability' => 100,
                'color' => '#22c55e',
            ],
        ];

        foreach ($stages as $stage) {
            // Verifica se já existe antes de inserir
            $exists = DB::table('pipeline_stages')
                ->where('name', $stage['name'])
                ->exists();

            if (!$exists) {
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
}
