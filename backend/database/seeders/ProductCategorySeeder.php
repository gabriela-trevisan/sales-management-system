<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Software', 'description' => 'Soluções de software e licenças'],
            ['name' => 'Hardware', 'description' => 'Equipamentos e periféricos'],
            ['name' => 'Serviços', 'description' => 'Consultoria e serviços profissionais'],
            ['name' => 'Suporte', 'description' => 'Planos de suporte técnico'],
            ['name' => 'Treinamento', 'description' => 'Cursos e capacitação'],
            ['name' => 'Cloud', 'description' => 'Serviços em nuvem'],
            ['name' => 'Licenças', 'description' => 'Licenças de uso'],
            ['name' => 'Infraestrutura', 'description' => 'Soluções de infraestrutura'],
        ];

        foreach ($categories as $category) {
            DB::table('product_categories')->insert([
                'name' => $category['name'],
                'description' => $category['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
