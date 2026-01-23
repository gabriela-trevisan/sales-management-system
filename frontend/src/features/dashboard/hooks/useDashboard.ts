import { useQuery, type UseQueryResult } from '@tanstack/react-query';
import { dashboardService, type DashboardMetrics, type Activity } from '@/features/dashboard/services/dashboardService';

/**
 * Hook para buscar métricas do dashboard usando TanStack Query.
 */
export function useDashboardMetrics(): UseQueryResult<DashboardMetrics, Error> {
  return useQuery({
    queryKey: ['dashboard', 'metrics'],
    queryFn: () => dashboardService.getMetrics(),
  });
}

/**
 * Hook para buscar atividades recentes usando TanStack Query.
 */
export function useRecentActivities(limit: number = 10): UseQueryResult<Activity[], Error> {
  return useQuery({
    queryKey: ['dashboard', 'activities', limit],
    queryFn: () => dashboardService.getRecentActivities(limit),
  });
}
