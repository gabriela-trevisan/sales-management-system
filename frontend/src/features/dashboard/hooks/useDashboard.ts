import { useQuery, type UseQueryResult } from '@tanstack/react-query';
import { dashboardService, type DashboardMetrics, type Activity, type SegmentData } from '@/features/dashboard/services/dashboardService';

/**
 * Hook para buscar métricas do dashboard usando TanStack Query.
 */
export function useDashboardMetrics(month?: string, year?: string): UseQueryResult<DashboardMetrics, Error> {
  return useQuery({
    queryKey: ['dashboard', 'metrics', month, year],
    queryFn: () => dashboardService.getMetrics(month, year),
  });
}

/**
 * Hook para buscar distribuição de clientes por segmento usando TanStack Query.
 */
export function useCustomersBySegment(month?: string, year?: string): UseQueryResult<SegmentData[], Error> {
  return useQuery({
    queryKey: ['dashboard', 'customers-by-segment', month, year],
    queryFn: () => dashboardService.getCustomersBySegment(month, year),
  });
}

/**
 * Hook para buscar atividades recentes usando TanStack Query.
 */
export function useRecentActivities(limit: number = 10, month?: string, year?: string): UseQueryResult<Activity[], Error> {
  return useQuery({
    queryKey: ['dashboard', 'activities', limit, month, year],
    queryFn: () => dashboardService.getRecentActivities(limit, month, year),
  });
}
