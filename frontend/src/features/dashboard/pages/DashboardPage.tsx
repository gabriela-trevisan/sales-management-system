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
        <h1 className="text-2xl font-bold text-oklch-on-surface">Dashboard</h1>
        <MonthlyPeriodSelector />
      </div>
      
      {/* Cards de Métricas */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <MetricCard
          title="Total de Clientes"
          value={metrics.total_customers}
          trend={metrics.total_customers_trend}
          icon={Users}
          iconColor="text-oklch-primary"
          loading={isLoading}
        />
        <MetricCard
          title="Oportunidades"
          value={metrics.total_opportunities}
          trend={metrics.total_opportunities_trend}
          icon={Target}
          iconColor="text-green-600"
          loading={isLoading}
        />
        <MetricCard
          title="Valor Pipeline"
          value={metrics.total_pipeline_value}
          trend={metrics.total_pipeline_value_trend}
          icon={DollarSign}
          iconColor="text-blue-600"
          formatValue="currency"
          loading={isLoading}
        />
        <MetricCard
          title="Taxa de Conversão"
          value={metrics.conversion_rate}
          trend={metrics.conversion_rate_trend}
          icon={TrendingUp}
          iconColor="text-amber-600"
          formatValue="percentage"
          loading={isLoading}
        />
      </div>

      {/* Gráficos */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {/* Gráfico de Clientes por Segmento (Donut) */}
        <CustomersBySegmentChart />

        {/* Gráfico de Vendas Mensais */}
        <Card className="bg-oklch-surface-container border-oklch-outline-variant">
          <CardHeader>
            <CardTitle className="text-base text-oklch-on-surface">Vendas Mensais</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <LineChart data={metrics.monthly_sales}>
                <CartesianGrid strokeDasharray="3 3" stroke="oklch(var(--outline-variant))" />
                <XAxis 
                  dataKey="month" 
                  stroke="oklch(var(--on-surface-variant))"
                  tick={{ fill: 'oklch(var(--on-surface))' }}
                />
                <YAxis 
                  stroke="oklch(var(--on-surface-variant))"
                  tick={{ fill: 'oklch(var(--on-surface))' }}
                />
                <Tooltip 
                  formatter={(value) => formatCurrency(Number(value))}
                  contentStyle={{
                    backgroundColor: 'oklch(var(--surface-container))',
                    border: '1px solid oklch(var(--outline-variant))',
                    borderRadius: '8px',
                    color: 'oklch(var(--on-surface))'
                  }}
                />
                <Legend wrapperStyle={{ color: 'oklch(var(--on-surface))' }} />
                <Line 
                  type="monotone" 
                  dataKey="value" 
                  stroke="oklch(var(--chart-2))"
                  strokeWidth={2}
                  name="Vendas"
                />
              </LineChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>

        {/* Gráfico de Oportunidades por Estágio */}
        <Card className="bg-oklch-surface-container border-oklch-outline-variant">
          <CardHeader>
            <CardTitle className="text-base text-oklch-on-surface">Oportunidades por Estágio</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={metrics.opportunities_by_stage}>
                <CartesianGrid strokeDasharray="3 3" stroke="oklch(var(--outline-variant))" />
                <XAxis 
                  dataKey="stage" 
                  stroke="oklch(var(--on-surface-variant))"
                  tick={{ fill: 'oklch(var(--on-surface))' }}
                />
                <YAxis 
                  stroke="oklch(var(--on-surface-variant))"
                  tick={{ fill: 'oklch(var(--on-surface))' }}
                />
                <Tooltip 
                  formatter={(value) => formatCurrency(Number(value))}
                  contentStyle={{
                    backgroundColor: 'oklch(var(--surface-container))',
                    border: '1px solid oklch(var(--outline-variant))',
                    borderRadius: '8px',
                    color: 'oklch(var(--on-surface))'
                  }}
                />
                <Legend wrapperStyle={{ color: 'oklch(var(--on-surface))' }} />
                <Bar dataKey="value" fill="oklch(var(--chart-3))" name="Valor Total" />
              </BarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>

      {/* Atividades Recentes */}
      <Card className="bg-oklch-surface-container border-oklch-outline-variant">
        <CardHeader>
          <div className="flex items-center gap-2">
            <Activity className="w-5 h-5 text-oklch-on-surface-variant" />
            <CardTitle className="text-base text-oklch-on-surface">Atividades Recentes</CardTitle>
          </div>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {activities.map((activity, index) => (
              <div key={index} className="flex items-start gap-4 pb-4 border-b border-oklch-outline-variant last:border-0">
                <div className={`p-2 rounded-full ${
                  activity.type === 'opportunity_created' ? 'bg-green-100' : 'bg-blue-100'
                }`}>
                  {activity.type === 'opportunity_created' ? (
                    <Target className="w-4 h-4 text-green-600" />
                  ) : (
                    <Users className="w-4 h-4 text-blue-600" />
                  )}
                </div>
                <div className="flex-1">
                  <p className="text-sm font-medium text-oklch-on-surface">{activity.description}</p>
                  <p className="text-xs text-oklch-on-surface-variant">{activity.user} • {formatDate(activity.created_at)}</p>
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
