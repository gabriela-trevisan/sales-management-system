<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@salesmanagement.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Gerente de Vendas
        User::firstOrCreate(
            ['email' => 'gabriela.trevisan@salesmanagement.com'],
            [
                'name' => 'Gabriela Trevisan',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Vendedores
        $vendedores = [
            ['name' => 'Ana Paula Santos', 'email' => 'ana.santos@salesmanagement.com'],
            ['name' => 'Bruno Oliveira', 'email' => 'bruno.oliveira@salesmanagement.com'],
            ['name' => 'Juliana Costa', 'email' => 'juliana.costa@salesmanagement.com'],
            ['name' => 'Rafael Mendes', 'email' => 'rafael.mendes@salesmanagement.com'],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda.lima@salesmanagement.com'],
        ];

        foreach ($vendedores as $vendedor) {
            User::firstOrCreate(
                ['email' => $vendedor['email']],
                [
                    'name' => $vendedor['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
