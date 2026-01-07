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
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@salesmanagement.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Gerente de Vendas
        User::create([
            'name' => 'Carlos Silva',
            'email' => 'carlos.silva@salesmanagement.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Vendedores
        $vendedores = [
            ['name' => 'Ana Paula Santos', 'email' => 'ana.santos@salesmanagement.com'],
            ['name' => 'Bruno Oliveira', 'email' => 'bruno.oliveira@salesmanagement.com'],
            ['name' => 'Juliana Costa', 'email' => 'juliana.costa@salesmanagement.com'],
            ['name' => 'Rafael Mendes', 'email' => 'rafael.mendes@salesmanagement.com'],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda.lima@salesmanagement.com'],
        ];

        foreach ($vendedores as $vendedor) {
            User::create([
                'name' => $vendedor['name'],
                'email' => $vendedor['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
