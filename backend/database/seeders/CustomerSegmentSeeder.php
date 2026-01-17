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
                'name' => 'Indústria e Manufatura',
                'description' => 'Empresas do setor industrial com necessidade de automação e sistemas customizados',
                'criteria' => json_encode([
                    'sector' => 'industry',
                    'typical_projects' => ['ERP', 'MES', 'IoT', 'Integração'],
                ]),
            ],
            [
                'name' => 'Serviços Financeiros',
                'description' => 'Bancos, fintechs, seguradoras com alta necessidade de segurança e compliance',
                'criteria' => json_encode([
                    'sector' => 'financial',
                    'typical_projects' => ['Core Banking', 'Open Banking', 'API Gateway', 'Segurança'],
                ]),
            ],
            [
                'name' => 'Varejo e E-commerce',
                'description' => 'Redes de varejo e comércio eletrônico',
                'criteria' => json_encode([
                    'sector' => 'retail',
                    'typical_projects' => ['E-commerce', 'PDV', 'Omnichannel', 'Integração Marketplace'],
                ]),
            ],
            [
                'name' => 'Saúde e Hospitais',
                'description' => 'Hospitais, clínicas, laboratórios com foco em prontuário e gestão',
                'criteria' => json_encode([
                    'sector' => 'healthcare',
                    'typical_projects' => ['Prontuário Eletrônico', 'TISS', 'Telemedicina', 'BI Saúde'],
                ]),
            ],
            [
                'name' => 'Logística e Transporte',
                'description' => 'Transportadoras, operadores logísticos com foco em rastreamento e gestão de frotas',
                'criteria' => json_encode([
                    'sector' => 'logistics',
                    'typical_projects' => ['TMS', 'Rastreamento', 'Roteirização', 'Last Mile'],
                ]),
            ],
            [
                'name' => 'Educação',
                'description' => 'Instituições de ensino, EdTechs com necessidade de plataformas educacionais',
                'criteria' => json_encode([
                    'sector' => 'education',
                    'typical_projects' => ['LMS', 'Portal do Aluno', 'EAD', 'Gestão Acadêmica'],
                ]),
            ],
        ];

        DB::table('customer_segments')->insert($segments);
    }
}
