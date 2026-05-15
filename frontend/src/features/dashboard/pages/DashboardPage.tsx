import { Users, TrendingUp, DollarSign, Target, Activity } from 'lucide-react';
import { LineChart, Line, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { useDashboardMetrics, useRecentActivities } from '../hooks/useDashboard';
import { DashboardPeriodProvider } from '@/contexts/DashboardPeriodContext';
import { useDashboardPeriod } from '@/hooks/useDashboardPeriod';
import { MonthlyPeriodSelector } from '@/components/dashboard/MonthlyPeriodSelector';
import { MetricCard } from '@/components/dashboard/MetricCard';
import { CustomersBySegmentChart } from '@/components/dashboard/CustomersBySegmentChart';

function DashboardContent() {
  const { month, year } = useDashboardPeriod();
  const { data: metrics, isLoading: isLoadingMetrics } = useDashboardMetrics(month, year);
  const { data: activities = [], isLoading: isLoadingActivities } = useRecentActivities(8, month, year);

  const isLoading = isLoadingMetrics || isLoadingActivities;

  if (isLoading || !metrics) {
    return (
      <div className="space-y-6">
        <Skeleton className="h-8 w-48" />
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Skeleton className="h-32" />
          <Skeleton className="h-32" />
          <Skeleton className="h-32" />
          <Skeleton className="h-32" />
        </div>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Skeleton className="h-96" />
          <Skeleton className="h-96" />
        </div>
      </div>
    );
  }

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(value);
  };

  const formatDate = (dateString: string) => {
    return new Intl.DateTimeFormat('pt-BR', {
      day: '2-digit',
      month: 'short',
      hour: '2-digit',
      minute: '2-digit'
    }).format(new Date(dateString));
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-foreground">Dashboard</h1>
        <MonthlyPeriodSelector />
      </div>
      
      {/* Cards de Métricas */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <MetricCard
          title="Total de Clientes"
          value={metrics.total_customers}
          trend={metrics.total_customers_trend}
          icon={Users}
          iconColor="text-primary"
          loading={isLoading}
        />
        <MetricCard
          title="Oportunidades"
          value={metrics.total_opportunities}
          trend={metrics.total_opportunities_trend}
          icon={Target}
          iconColor="text-success"
          loading={isLoading}
        />
        <MetricCard
          title="Valor Pipeline"
          value={metrics.total_pipeline_value}
          trend={metrics.total_pipeline_value_trend}
          icon={DollarSign}
          iconColor="text-info"
          formatValue="currency"
          loading={isLoading}
        />
        <MetricCard
          title="Taxa de Conversão"
          value={metrics.conversion_rate}
          trend={metrics.conversion_rate_trend}
          icon={TrendingUp}
          iconColor="text-warning"
          formatValue="percentage"
          loading={isLoading}
        />
      </div>

      {/* Gráficos */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {/* Gráfico de Clientes por Segmento (Donut) */}
        <CustomersBySegmentChart />

        {/* Gráfico de Vendas Mensais */}
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Vendas Mensais (últimos 6 meses)</CardTitle>
          </CardHeader>
          <CardContent>
            {metrics.monthly_sales.length === 0 ? (
              <div className="flex items-center justify-center h-[300px] text-muted-foreground text-sm">
                Nenhuma venda fechada no período
              </div>
            ) : (
              <ResponsiveContainer width="100%" height={300}>
                <LineChart data={metrics.monthly_sales}>
                  <CartesianGrid strokeDasharray="3 3" stroke="var(--color-border)" />
                  <XAxis
                    dataKey="month"
                    stroke="var(--color-muted-foreground)"
                    tick={{ fill: 'var(--color-foreground)', fontSize: 12 }}
                  />
                  <YAxis
                    stroke="var(--color-muted-foreground)"
                    tick={{ fill: 'var(--color-foreground)', fontSize: 12 }}
                    tickFormatter={(v: number) => {
                      if (v >= 1_000_000) return `R$${(v / 1_000_000).toFixed(1)}M`;
                      if (v >= 1_000) return `R$${(v / 1_000).toFixed(0)}K`;
                      return `R$${v}`;
                    }}
                    width={72}
                  />
                  <Tooltip
                    formatter={(value) => [formatCurrency(Number(value)), 'Vendas']}
                    contentStyle={{
                      backgroundColor: 'var(--color-card)',
                      border: '1px solid var(--color-border)',
                      borderRadius: '8px',
                      color: 'var(--color-foreground)'
                    }}
                  />
                  <Legend wrapperStyle={{ color: 'var(--color-foreground)' }} />
                  <Line
                    type="monotone"
                    dataKey="value"
                    stroke="var(--color-chart-2)"
                    strokeWidth={2}
                    dot={{ fill: 'var(--color-chart-2)', r: 4 }}
                    activeDot={{ r: 6 }}
                    name="Vendas"
                  />
                </LineChart>
              </ResponsiveContainer>
            )}
          </CardContent>
        </Card>

        {/* Gráfico de Oportunidades por Estágio */}
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Oportunidades por Estágio</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={metrics.opportunities_by_stage}>
                <CartesianGrid strokeDasharray="3 3" stroke="var(--color-border)" />
                <XAxis 
                  dataKey="stage" 
                  stroke="var(--color-muted-foreground)"
                  tick={{ fill: 'var(--color-foreground)' }}
                />
                <YAxis 
                  stroke="var(--color-muted-foreground)"
                  tick={{ fill: 'var(--color-foreground)' }}
                />
                <Tooltip 
                  formatter={(value) => formatCurrency(Number(value))}
                  contentStyle={{
                    backgroundColor: 'var(--color-card)',
                    border: '1px solid var(--color-border)',
                    borderRadius: '8px',
                    color: 'var(--color-foreground)'
                  }}
                />
                <Legend wrapperStyle={{ color: 'var(--color-foreground)' }} />
                <Bar dataKey="value" fill="var(--color-chart-3)" name="Valor Total" />
              </BarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>

      {/* Atividades Recentes */}
      <Card>
        <CardHeader>
          <div className="flex items-center gap-2">
            <Activity className="w-5 h-5 text-muted-foreground" />
            <CardTitle className="text-base">Atividades Recentes</CardTitle>
          </div>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {activities.map((activity, index) => (
              <div key={index} className="flex items-start gap-4 pb-4 border-b border-border last:border-0">
                <div className={`p-2 rounded-full ${
                  activity.type === 'opportunity_created' ? 'bg-success/10' : 'bg-primary/10'
                }`}>
                  {activity.type === 'opportunity_created' ? (
                    <Target className="w-4 h-4 text-success" />
                  ) : (
                    <Users className="w-4 h-4 text-primary" />
                  )}
                </div>
                <div className="flex-1">
                  <p className="text-sm font-medium text-foreground">{activity.description}</p>
                  <p className="text-xs text-muted-foreground">{activity.user} • {formatDate(activity.created_at)}</p>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}

export function DashboardPage() {
  return (
    <DashboardPeriodProvider>
      <DashboardContent />
    </DashboardPeriodProvider>
  );
}
