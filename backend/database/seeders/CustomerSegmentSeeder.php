<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $segments = [
            [
                'name' => 'Pequenas Empresas',
                'description' => 'Empresas com faturamento até R$ 360 mil/ano',
                'criteria' => json_encode([
                    'revenue_max' => 360000,
                    'employees_max' => 10,
                ]),
            ],
            [
                'name' => 'Médias Empresas',
                'description' => 'Empresas com faturamento entre R$ 360 mil e R$ 4,8 milhões/ano',
                'criteria' => json_encode([
                    'revenue_min' => 360000,
                    'revenue_max' => 4800000,
                    'employees_max' => 50,
                ]),
            ],
            [
                'name' => 'Grandes Empresas',
                'description' => 'Empresas com faturamento acima de R$ 4,8 milhões/ano',
                'criteria' => json_encode([
                    'revenue_min' => 4800000,
                    'employees_min' => 50,
                ]),
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Grandes corporações com operações complexas',
                'criteria' => json_encode([
                    'revenue_min' => 50000000,
                    'employees_min' => 500,
                ]),
            ],
            [
                'name' => 'Startups',
                'description' => 'Empresas em estágio inicial de crescimento',
                'criteria' => json_encode([
                    'age_max' => 3,
                    'revenue_max' => 1000000,
                ]),
            ],
        ];

        DB::table('customer_segments')->insert($segments);
    }
}
