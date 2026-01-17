<?php

namespace Database\Seeders;

use App\Models\User;
use App\Domain\Customer\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Popula a tabela de clientes com dados de teste.
     * 
     * Todos os CNPJs são matematicamente válidos.
     */
    public function run(): void
    {
        $users = User::all();
        
        $customers = [
            [
                'name' => 'Tech Solutions Ltda',
                'document' => '11.222.333/0001-81',
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
                'document' => '11.444.777/0001-61',
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
                'document' => '16.727.230/0001-97',
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
                'document' => '07.526.557/0001-00',
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
                'document' => '34.028.316/0001-03',
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
            $customer = Customer::create([
                'name' => $customerData['name'],
                'document' => $customerData['document'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'segment_id' => $customerData['segment_id'],
                'status' => $customerData['status'],
                'assigned_to' => $customerData['assigned_to'],
            ]);

            DB::table('customer_addresses')->insert([
                'customer_id' => $customer->id,
                'type' => 'both',
                'zip_code' => preg_replace('/[^0-9]/', '', $customerData['address']['zip_code']),
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

            DB::table('customer_contacts')->insert([
                'customer_id' => $customer->id,
                'name' => $customerData['contact']['name'],
                'email' => strtolower(trim($customerData['contact']['email'])),
                'phone' => preg_replace('/[^0-9]/', '', $customerData['contact']['phone']),
                'role' => $customerData['contact']['role'],
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
