import api from '@/services/api';

/**
 * Métricas do dashboard.
 */
export interface DashboardMetrics {
  total_customers: number;
  total_opportunities: number;
  total_pipeline_value: number;
  conversion_rate: number;
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
  async getMetrics(): Promise<DashboardMetrics> {
    const response = await api.get('/dashboard/metrics');
    return response.data;
  },

  /**
   * Busca atividades recentes do sistema.
   */
  async getRecentActivities(limit: number = 10): Promise<Activity[]> {
    const response = await api.get('/dashboard/recent-activities', {
      params: { limit }
    });
    return response.data.activities;
  },
};
