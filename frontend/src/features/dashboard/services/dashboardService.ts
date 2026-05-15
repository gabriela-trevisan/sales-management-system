import api from '@/services/api';

/**
 * Métricas do dashboard.
 */
export interface DashboardMetrics {
  total_customers: number;
  total_customers_previous?: number;
  total_customers_trend?: number;
  total_opportunities: number;
  total_opportunities_previous?: number;
  total_opportunities_trend?: number;
  total_pipeline_value: number;
  total_pipeline_value_trend?: number;
  conversion_rate: number;
  conversion_rate_trend?: number;
  monthly_sales: Array<{
    month: string;
    value: number;
  }>;
  opportunities_by_stage: Array<{
    stage: string;
    count: number;
    value: number;
  }>;
}

/**
 * Segmento de cliente com contagem e percentual.
 */
export interface SegmentData {
  name: string;
  count: number;
  percentage: number;
}

/**
 * Atividade recente do sistema.
 */
export interface Activity {
  type: string;
  description: string;
  user: string;
  created_at: string;
}

/**
 * Serviço para dados do dashboard.
 */
export const dashboardService = {
  /**
   * Busca métricas gerais do sistema.
   */
  async getMetrics(month?: string, year?: string): Promise<DashboardMetrics> {
    const response = await api.get('/dashboard/metrics', {
      params: month && year ? { month, year } : undefined
    });
    return response.data;
  },

  /**
   * Busca distribuição de clientes por segmento.
   */
  async getCustomersBySegment(month?: string, year?: string): Promise<SegmentData[]> {
    const response = await api.get('/dashboard/customers-by-segment', {
      params: month && year ? { month, year } : undefined
    });
    return response.data.segments ?? [];
  },

  /**
   * Busca atividades recentes do sistema.
   */
  async getRecentActivities(limit: number = 10, month?: string, year?: string): Promise<Activity[]> {
    const response = await api.get('/dashboard/recent-activities', {
      params: {
        limit,
        ...(month && year ? { month, year } : {})
      }
    });
    return response.data.activities;
  },
};
