<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        $customers = [
            [
                'name' => 'Tech Solutions Ltda',
                'document' => '12.345.678/0001-90',
                'email' => 'contato@techsolutions.com.br',
                'phone' => '(11) 3456-7890',
                'segment_id' => 2,
                'status' => 'active',
                'assigned_to' => $users->random()->id,
                'address' => [
                    'zip_code' => '01310-100',
                    'street' => 'Avenida Paulista',
                    'number' => '1578',
                    'neighborhood' => 'Bela Vista',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                ],
                'contact' => [
                    'name' => 'João Pedro Silva',
                    'email' => 'joao.silva@techsolutions.com.br',
                    'phone' => '(11) 98765-4321',
                    'role' => 'Diretor de TI',
                ],
            ],
            [
                'name' => 'Inovação Digital S.A.',
                'document' => '98.765.432/0001-10',
                'email' => 'comercial@inovacaodigital.com.br',
                'phone' => '(21) 2345-6789',
                'segment_id' => 3,
                'status' => 'active',
                'assigned_to' => $users->random()->id,
                'address' => [
                    'zip_code' => '20040-020',
                    'street' => 'Avenida Rio Branco',
                    'number' => '156',
                    'neighborhood' => 'Centro',
                    'city' => 'Rio de Janeiro',
                    'state' => 'RJ',
                ],
                'contact' => [
                    'name' => 'Maria Fernanda Costa',
                    'email' => 'maria.costa@inovacaodigital.com.br',
                    'phone' => '(21) 99876-5432',
                    'role' => 'Gerente de Compras',
                ],
            ],
            [
                'name' => 'Cloud Systems Brasil',
                'document' => '45.678.901/0001-23',
                'email' => 'vendas@cloudsystems.com.br',
                'phone' => '(11) 4567-8901',
                'segment_id' => 2,
                'status' => 'active',
                'assigned_to' => $users->random()->id,
                'address' => [
                    'zip_code' => '04543-011',
                    'street' => 'Avenida Brigadeiro Faria Lima',
                    'number' => '3477',
                    'neighborhood' => 'Itaim Bibi',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                ],
                'contact' => [
                    'name' => 'Carlos Eduardo Santos',
                    'email' => 'carlos.santos@cloudsystems.com.br',
                    'phone' => '(11) 97654-3210',
                    'role' => 'CTO',
                ],
            ],
            [
                'name' => 'StartupTech Inovação',
                'document' => '23.456.789/0001-45',
                'email' => 'contato@startuptech.com.br',
                'phone' => '(48) 3234-5678',
                'segment_id' => 5,
                'status' => 'prospect',
                'assigned_to' => $users->random()->id,
                'address' => [
                    'zip_code' => '88015-100',
                    'street' => 'Avenida Beira Mar Norte',
                    'number' => '500',
                    'neighborhood' => 'Centro',
                    'city' => 'Florianópolis',
                    'state' => 'SC',
                ],
                'contact' => [
                    'name' => 'Ana Paula Rodrigues',
                    'email' => 'ana.rodrigues@startuptech.com.br',
                    'phone' => '(48) 99123-4567',
                    'role' => 'CEO',
                ],
            ],
            [
                'name' => 'Mega Corp Tecnologia',
                'document' => '67.890.123/0001-56',
                'email' => 'contato@megacorp.com.br',
                'phone' => '(31) 3567-8901',
                'segment_id' => 4,
                'status' => 'active',
                'assigned_to' => $users->random()->id,
                'address' => [
                    'zip_code' => '30130-100',
                    'street' => 'Avenida Afonso Pena',
                    'number' => '2881',
                    'neighborhood' => 'Funcionários',
                    'city' => 'Belo Horizonte',
                    'state' => 'MG',
                ],
                'contact' => [
                    'name' => 'Roberto Alves',
                    'email' => 'roberto.alves@megacorp.com.br',
                    'phone' => '(31) 98234-5678',
                    'role' => 'Diretor Executivo',
                ],
            ],
        ];

        foreach ($customers as $customerData) {
            $customerId = DB::table('customers')->insertGetId([
                'name' => $customerData['name'],
                'document' => $customerData['document'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'segment_id' => $customerData['segment_id'],
                'status' => $customerData['status'],
                'assigned_to' => $customerData['assigned_to'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Endereço
            DB::table('customer_addresses')->insert([
                'customer_id' => $customerId,
                'type' => 'both',
                'zip_code' => $customerData['address']['zip_code'],
                'street' => $customerData['address']['street'],
                'number' => $customerData['address']['number'],
                'neighborhood' => $customerData['address']['neighborhood'],
                'city' => $customerData['address']['city'],
                'state' => $customerData['address']['state'],
                'country' => 'BR',
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Contato
            DB::table('customer_contacts')->insert([
                'customer_id' => $customerId,
                'name' => $customerData['contact']['name'],
                'email' => $customerData['contact']['email'],
                'phone' => $customerData['contact']['phone'],
                'role' => $customerData['contact']['role'],
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
