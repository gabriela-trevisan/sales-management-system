<?php

namespace App\Presentation\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;
use Carbon\Carbon;

/**
 * Dashboard Controller
 * 
 * Controlador responsável por fornecer métricas e visualizações do dashboard.
 * Suporta filtros mensais e cálculo de tendências comparativas.
 * 
 * @package App\Presentation\Http\Controllers\API\Dashboard
 */
class DashboardController extends Controller
{
    /**
     * Retorna a distribuição de clientes por segmento de mercado.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(
        path: "/api/dashboard/customers-by-segment",
        summary: "Clientes por segmento",
        description: "Retorna a distribuição de clientes por segmento de mercado com suporte a filtro mensal",
        security: [["bearerAuth" => []]],
        tags: ["Dashboard"],
        parameters: [
            new OA\Parameter(
                name: "month",
                in: "query",
                description: "Mês para filtro (01-12)",
                required: false,
                schema: new OA\Schema(type: "string", example: "01")
            ),
            new OA\Parameter(
                name: "year",
                in: "query",
                description: "Ano para filtro",
                required: false,
                schema: new OA\Schema(type: "string", example: "2026")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Distribuição de clientes por segmento",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "segments",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "name", type: "string", example: "Indústria"),
                                    new OA\Property(property: "count", type: "integer", example: 25),
                                    new OA\Property(property: "percentage", type: "number", format: "float", example: 35.7)
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
    public function customersBySegment(Request $request): JsonResponse
    {
        $rawMonth = $request->query('month');
        $rawYear = $request->query('year');
        $month = is_string($rawMonth) ? $rawMonth : null;
        $year = is_string($rawYear) ? $rawYear : null;
        
        $cacheKey = "dashboard.customers_by_segment.{$month}.{$year}";
        
        return Cache::remember($cacheKey, 300, function () use ($month, $year) {
            $query = DB::table('customers')
                ->join('customer_segments', 'customers.segment_id', '=', 'customer_segments.id')
                ->select(
                    'customer_segments.name',
                    DB::raw('COUNT(customers.id) as count')
                )
                ->whereNull('customers.deleted_at')
                ->groupBy('customer_segments.id', 'customer_segments.name');
            
            if ($month && $year) {
                $startDate = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate((int) $year, (int) $month, 1)->endOfMonth();
                
                $query->whereBetween('customers.created_at', [$startDate, $endDate]);
            }
            
            $segments = $query->get();
            
            $total = $segments->sum('count');
            
            $result = $segments->map(function ($segment) use ($total) {
                return [
                    'name' => $segment->name,
                    'count' => $segment->count,
                    'percentage' => $total > 0 ? round(($segment->count / $total) * 100, 1) : 0
                ];
            });
            
            return response()->json([
                'segments' => $result
            ]);
        });
    }

    /**
     * Retorna métricas principais do dashboard com suporte a comparação mensal.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(
        path: "/api/dashboard/metrics",
        summary: "Métricas do dashboard",
        description: "Retorna as principais métricas do sistema com suporte a filtro mensal e comparação com mês anterior",
        security: [["bearerAuth" => []]],
        tags: ["Dashboard"],
        parameters: [
            new OA\Parameter(
                name: "month",
                in: "query",
                description: "Mês para filtro (01-12)",
                required: false,
                schema: new OA\Schema(type: "string", example: "01")
            ),
            new OA\Parameter(
                name: "year",
                in: "query",
                description: "Ano para filtro",
                required: false,
                schema: new OA\Schema(type: "string", example: "2026")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Métricas do dashboard",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "total_customers", type: "integer", example: 150),
                        new OA\Property(property: "total_customers_previous", type: "integer", example: 135),
                        new OA\Property(property: "total_customers_trend", type: "number", format: "float", example: 11.1),
                        new OA\Property(property: "total_opportunities", type: "integer", example: 45),
                        new OA\Property(property: "total_opportunities_previous", type: "integer", example: 38),
                        new OA\Property(property: "total_opportunities_trend", type: "number", format: "float", example: 18.4),
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
    public function metrics(Request $request): JsonResponse
    {
        $rawMonth = $request->query('month');
        $rawYear = $request->query('year');
        $month = is_string($rawMonth) ? $rawMonth : null;
        $year = is_string($rawYear) ? $rawYear : null;
        
        $cacheKey = "dashboard.metrics.{$month}.{$year}";
        
        return Cache::remember($cacheKey, 300, function () use ($month, $year) {
            return $this->calculateMetrics($month, $year);
        });
    }

    /**
     * Retorna atividades recentes do sistema.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(
        path: "/api/dashboard/recent-activities",
        summary: "Atividades recentes",
        description: "Retorna as últimas atividades do sistema com suporte a filtro mensal",
        security: [["bearerAuth" => []]],
        tags: ["Dashboard"],
        parameters: [
            new OA\Parameter(
                name: "limit",
                in: "query",
                description: "Quantidade de atividades a retornar",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            ),
            new OA\Parameter(
                name: "month",
                in: "query",
                description: "Mês para filtro (01-12)",
                required: false,
                schema: new OA\Schema(type: "string", example: "01")
            ),
            new OA\Parameter(
                name: "year",
                in: "query",
                description: "Ano para filtro",
                required: false,
                schema: new OA\Schema(type: "string", example: "2026")
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
    public function recentActivities(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', '10'), 1), 100);
        $rawMonth = $request->query('month');
        $rawYear = $request->query('year');
        $month = is_string($rawMonth) ? $rawMonth : null;
        $year = is_string($rawYear) ? $rawYear : null;

        $startDate = null;
        $endDate = null;
        if ($month && $year) {
            $startDate = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate((int) $year, (int) $month, 1)->endOfMonth();
        }

        $opportunitiesQuery = DB::table('opportunities')
            ->join('customers', 'opportunities.customer_id', '=', 'customers.id')
            ->join('users', 'opportunities.assigned_to', '=', 'users.id')
            ->select(
                DB::raw("'opportunity_created' as type"),
                DB::raw("CONCAT('Nova oportunidade: ', opportunities.title) as description"),
                'users.name as user',
                'opportunities.created_at'
            )
            ->whereNull('opportunities.deleted_at')
            ->whereNull('customers.deleted_at')
            ->whereNotNull('opportunities.assigned_to');
        
        if ($startDate !== null) {
            $opportunitiesQuery->whereBetween('opportunities.created_at', [$startDate, $endDate]);
        }
        
        $recentOpportunities = $opportunitiesQuery
            ->orderBy('opportunities.created_at', 'desc')
            ->limit((int) ceil($limit / 2))
            ->get();

        $customersQuery = DB::table('customers')
            ->leftJoin('users', 'customers.assigned_to', '=', 'users.id')
            ->select(
                DB::raw("'customer_created' as type"),
                DB::raw("CONCAT('Novo cliente: ', customers.name) as description"),
                DB::raw("COALESCE(users.name, 'Sistema') as user"),
                'customers.created_at'
            )
            ->whereNull('customers.deleted_at');
        
        if ($startDate !== null) {
            $customersQuery->whereBetween('customers.created_at', [$startDate, $endDate]);
        }
        
        $recentCustomers = $customersQuery
            ->orderBy('customers.created_at', 'desc')
            ->limit((int) ceil($limit / 2))
            ->get();

        $activities = collect($recentOpportunities)
            ->merge($recentCustomers)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();

        return response()->json([
            'activities' => $activities
        ]);
    }

    /**
     * Calcula as métricas do dashboard com filtro de período opcional.
     * Inclui comparação com mês anterior para cálculo de tendências.
     * 
     * @param string|null $month Mês para filtro (01-12)
     * @param string|null $year Ano para filtro
     * @return JsonResponse
     */
    private function calculateMetrics(?string $month, ?string $year): JsonResponse
    {
        $currentStart = $month && $year 
            ? Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth()
            : Carbon::now()->startOfMonth();
        $currentEnd = $month && $year
            ? Carbon::createFromDate((int) $year, (int) $month, 1)->endOfMonth()
            : Carbon::now()->endOfMonth();
        
        $previousStart = (clone $currentStart)->subMonth()->startOfMonth();
        $previousEnd = (clone $currentStart)->subMonth()->endOfMonth();

        // total de clientes acumulado até o fim do período atual (não apenas criados nele)
        $totalCustomers = DB::table('customers')
            ->where('created_at', '<=', $currentEnd)
            ->whereNull('deleted_at')
            ->count();
        
        // total de clientes acumulado até o fim do período anterior (base para calcular tendência)
        $totalCustomersPrevious = DB::table('customers')
            ->where('created_at', '<=', $previousEnd)
            ->whereNull('deleted_at')
            ->count();
        
        $customersTrend = $totalCustomersPrevious > 0
            ? round((($totalCustomers - $totalCustomersPrevious) / $totalCustomersPrevious) * 100, 1)
            : 0;

        $totalOpportunities = DB::table('opportunities')
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->whereNull('deleted_at')
            ->count();
        
        $totalOpportunitiesPrevious = DB::table('opportunities')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->whereNull('deleted_at')
            ->count();
        
        $opportunitiesTrend = $totalOpportunitiesPrevious > 0
            ? round((($totalOpportunities - $totalOpportunitiesPrevious) / $totalOpportunitiesPrevious) * 100, 1)
            : 0;

        // valor total do pipeline inclui apenas oportunidades com status 'open'
        $totalPipelineValue = DB::table('opportunities')
            ->where('status', 'open')
            ->whereNull('deleted_at')
            ->sum('value');

        // valor do pipeline novo criado no período atual vs anterior (para tendência)
        $pipelineValueCurrent = DB::table('opportunities')
            ->where('status', 'open')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->sum('value');

        $pipelineValuePrevious = DB::table('opportunities')
            ->where('status', 'open')
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('value');

        $pipelineValueTrend = $pipelineValuePrevious > 0
            ? round((((float) $pipelineValueCurrent - (float) $pipelineValuePrevious) / (float) $pipelineValuePrevious) * 100, 1)
            : 0;

        $wonOpportunities = DB::table('opportunities')
            ->where('status', 'won')
            ->whereNull('deleted_at')
            ->whereBetween('closed_at', [$currentStart, $currentEnd])
            ->count();
        $conversionRate = $totalOpportunities > 0 
            ? round(($wonOpportunities / $totalOpportunities) * 100, 1) 
            : 0;

        $wonOpportunitiesPrevious = DB::table('opportunities')
            ->where('status', 'won')
            ->whereNull('deleted_at')
            ->whereBetween('closed_at', [$previousStart, $previousEnd])
            ->count();
        $conversionRatePrevious = $totalOpportunitiesPrevious > 0
            ? round(($wonOpportunitiesPrevious / $totalOpportunitiesPrevious) * 100, 1)
            : 0;

        $conversionRateTrend = $conversionRatePrevious > 0
            ? round((($conversionRate - $conversionRatePrevious) / $conversionRatePrevious) * 100, 1)
            : 0;

        $monthNames = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        // Janela de 6 meses encerrada no período selecionado (ou mês atual)
        $salesWindowStart = (clone $currentEnd)->subMonths(5)->startOfMonth();

        $monthlySales = DB::table('opportunities')
            ->select(
                DB::raw("DATE_FORMAT(closed_at, '%Y-%m') as month_key"),
                DB::raw("MONTH(closed_at) as month_number"),
                DB::raw("DATE_FORMAT(closed_at, '%y') as year_short"),
                DB::raw('SUM(value) as value')
            )
            ->where('status', 'won')
            ->whereNull('deleted_at')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$salesWindowStart, $currentEnd])
            ->groupBy(DB::raw("DATE_FORMAT(closed_at, '%Y-%m')"), DB::raw("MONTH(closed_at)"), DB::raw("DATE_FORMAT(closed_at, '%y')"))
            ->orderBy(DB::raw("DATE_FORMAT(closed_at, '%Y-%m')"))
            ->get()
            ->map(function ($item) use ($monthNames) {
                return [
                    'month' => $monthNames[(int) $item->month_number - 1] . '/' . $item->year_short,
                    'value' => (float) $item->value,
                ];
            });

        $opportunitiesByStage = DB::table('opportunities')
            ->join('pipeline_stages', 'opportunities.pipeline_stage_id', '=', 'pipeline_stages.id')
            ->select(
                'pipeline_stages.name as stage',
                DB::raw('COUNT(opportunities.id) as count'),
                DB::raw('SUM(opportunities.value) as value')
            )
            ->where('opportunities.status', 'open')
            ->whereNull('opportunities.deleted_at')
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
            'total_customers_previous' => $totalCustomersPrevious,
            'total_customers_trend' => $customersTrend,
            'total_opportunities' => $totalOpportunities,
            'total_opportunities_previous' => $totalOpportunitiesPrevious,
            'total_opportunities_trend' => $opportunitiesTrend,
            'total_pipeline_value' => (float) $totalPipelineValue,
            'total_pipeline_value_trend' => $pipelineValueTrend,
            'conversion_rate' => $conversionRate,
            'conversion_rate_trend' => $conversionRateTrend,
            'monthly_sales' => $monthlySales,
            'opportunities_by_stage' => $opportunitiesByStage
        ]);
    }
}
