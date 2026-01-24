<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class DashboardController extends Controller
{
    #[OA\Get(
        path: "/api/dashboard/metrics",
        summary: "Métricas do dashboard",
        description: "Retorna as principais métricas do sistema: total de clientes, oportunidades, valor total do pipeline e taxa de conversão",
        security: [["bearerAuth" => []]],
        tags: ["Dashboard"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Métricas do dashboard",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "total_customers", type: "integer", example: 150),
                        new OA\Property(property: "total_opportunities", type: "integer", example: 45),
                        new OA\Property(property: "total_pipeline_value", type: "number", format: "float", example: 450000.00),
                        new OA\Property(property: "conversion_rate", type: "number", format: "float", example: 32.5),
                        new OA\Property(
                            property: "monthly_sales",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "month", type: "string", example: "Jan"),
                                    new OA\Property(property: "value", type: "number", example: 45000)
                                ],
                                type: "object"
                            )
                        ),
                        new OA\Property(
                            property: "opportunities_by_stage",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "stage", type: "string", example: "Qualificação"),
                                    new OA\Property(property: "count", type: "integer", example: 12),
                                    new OA\Property(property: "value", type: "number", example: 120000)
                                ],
                                type: "object"
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado")
        ]
    )]
    public function metrics(): JsonResponse
    {
        // Cache por 5 minutos (300 segundos)
        return Cache::remember('dashboard.metrics', 300, function () {
            return $this->calculateMetrics();
        });
    }

    /**
     * Calcula as métricas do dashboard.
     * Método separado para facilitar testes e invalidação de cache.
     */
    private function calculateMetrics(): JsonResponse
    {
        // Total de clientes
        $totalCustomers = DB::table('customers')->count();

        // Total de oportunidades
        $totalOpportunities = DB::table('opportunities')->count();

        // Valor total do pipeline (oportunidades abertas)
        $totalPipelineValue = DB::table('opportunities')
            ->where('status', 'open')
            ->sum('value');

        // Taxa de conversão (oportunidades ganhas / total)
        $wonOpportunities = DB::table('opportunities')
            ->where('status', 'won')
            ->count();
        $conversionRate = $totalOpportunities > 0 
            ? round(($wonOpportunities / $totalOpportunities) * 100, 1) 
            : 0;

        // Vendas dos últimos 6 meses
        $monthlySales = DB::table('opportunities')
            ->select(
                DB::raw("DATE_FORMAT(closed_at, '%b') as month"),
                DB::raw('SUM(value) as value')
            )
            ->where('status', 'won')
            ->whereNotNull('closed_at')
            ->where('closed_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("DATE_FORMAT(closed_at, '%Y-%m')"), DB::raw("DATE_FORMAT(closed_at, '%b')"))
            ->orderBy(DB::raw("DATE_FORMAT(closed_at, '%Y-%m')"))
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'value' => (float) $item->value
                ];
            });

        // Oportunidades por estágio (usando pipeline_stages)
        $opportunitiesByStage = DB::table('opportunities')
            ->join('pipeline_stages', 'opportunities.pipeline_stage_id', '=', 'pipeline_stages.id')
            ->select(
                'pipeline_stages.name as stage',
                DB::raw('COUNT(opportunities.id) as count'),
                DB::raw('SUM(opportunities.value) as value')
            )
            ->where('opportunities.status', 'open')
            ->groupBy('pipeline_stages.id', 'pipeline_stages.name')
            ->orderBy('pipeline_stages.order')
            ->get()
            ->map(function ($item) {
                return [
                    'stage' => $item->stage,
                    'count' => $item->count,
                    'value' => (float) $item->value
                ];
            });

        return response()->json([
            'total_customers' => $totalCustomers,
            'total_opportunities' => $totalOpportunities,
            'total_pipeline_value' => (float) $totalPipelineValue,
            'conversion_rate' => $conversionRate,
            'monthly_sales' => $monthlySales,
            'opportunities_by_stage' => $opportunitiesByStage
        ]);
    }

    #[OA\Get(
        path: "/api/dashboard/recent-activities",
        summary: "Atividades recentes",
        description: "Retorna as últimas atividades do sistema (oportunidades criadas, clientes cadastrados, etc)",
        security: [["bearerAuth" => []]],
        tags: ["Dashboard"],
        parameters: [
            new OA\Parameter(
                name: "limit",
                in: "query",
                description: "Quantidade de atividades a retornar",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de atividades recentes",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "activities",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "type", type: "string", example: "opportunity_created"),
                                    new OA\Property(property: "description", type: "string", example: "Nova oportunidade criada"),
                                    new OA\Property(property: "user", type: "string", example: "João Silva"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-17T10:30:00Z")
                                ],
                                type: "object"
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado")
        ]
    )]
    public function recentActivities(): JsonResponse
    {
        $limit = request()->get('limit', 10);

        // Buscar últimas oportunidades criadas
        $recentOpportunities = DB::table('opportunities')
            ->join('customers', 'opportunities.customer_id', '=', 'customers.id')
            ->join('users', 'opportunities.assigned_to', '=', 'users.id')
            ->select(
                DB::raw("'opportunity_created' as type"),
                DB::raw("CONCAT('Nova oportunidade: ', opportunities.title) as description"),
                'users.name as user',
                'opportunities.created_at'
            )
            ->whereNotNull('opportunities.assigned_to')
            ->orderBy('opportunities.created_at', 'desc')
            ->limit((int) ceil($limit / 2))
            ->get();

        // Buscar últimos clientes cadastrados
        $recentCustomers = DB::table('customers')
            ->leftJoin('users', 'customers.assigned_to', '=', 'users.id')
            ->select(
                DB::raw("'customer_created' as type"),
                DB::raw("CONCAT('Novo cliente: ', customers.name) as description"),
                DB::raw("COALESCE(users.name, 'Sistema') as user"),
                'customers.created_at'
            )
            ->orderBy('customers.created_at', 'desc')
            ->limit((int) ceil($limit / 2))
            ->get();

        // Combinar e ordenar por data
        $activities = collect($recentOpportunities)
            ->merge($recentCustomers)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();

        return response()->json([
            'activities' => $activities
        ]);
    }
}
