import { Users, TrendingUp, DollarSign, Target, Activity } from 'lucide-react';
import { LineChart, Line, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import { useDashboardMetrics, useRecentActivities } from '../hooks/useDashboard';

export function DashboardPage() {
  const { data: metrics, isLoading: isLoadingMetrics } = useDashboardMetrics();
  const { data: activities = [], isLoading: isLoadingActivities } = useRecentActivities(8);

  const isLoading = isLoadingMetrics || isLoadingActivities;

  if (isLoading || !metrics) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
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
      <h1 className="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>
      
      {/* Cards de Métricas */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center justify-between mb-4">
            <div>
              <p className="text-sm text-gray-600">Total de Clientes</p>
              <p className="text-3xl font-bold text-gray-900">{metrics.total_customers}</p>
            </div>
            <div className="bg-blue-100 p-3 rounded-full">
              <Users className="w-6 h-6 text-blue-600" />
            </div>
          </div>
          <p className="text-xs text-gray-500">Base de clientes cadastrada</p>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center justify-between mb-4">
            <div>
              <p className="text-sm text-gray-600">Oportunidades</p>
              <p className="text-3xl font-bold text-gray-900">{metrics.total_opportunities}</p>
            </div>
            <div className="bg-green-100 p-3 rounded-full">
              <Target className="w-6 h-6 text-green-600" />
            </div>
          </div>
          <p className="text-xs text-gray-500">Em negociação</p>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center justify-between mb-4">
            <div>
              <p className="text-sm text-gray-600">Valor Pipeline</p>
              <p className="text-3xl font-bold text-gray-900">
                {formatCurrency(metrics.total_pipeline_value)}
              </p>
            </div>
            <div className="bg-purple-100 p-3 rounded-full">
              <DollarSign className="w-6 h-6 text-purple-600" />
            </div>
          </div>
          <p className="text-xs text-gray-500">Valor total em negociação</p>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center justify-between mb-4">
            <div>
              <p className="text-sm text-gray-600">Taxa de Conversão</p>
              <p className="text-3xl font-bold text-gray-900">{metrics.conversion_rate}%</p>
            </div>
            <div className="bg-orange-100 p-3 rounded-full">
              <TrendingUp className="w-6 h-6 text-orange-600" />
            </div>
          </div>
          <p className="text-xs text-gray-500">Oportunidades ganhas</p>
        </div>
      </div>

      {/* Gráficos */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {/* Gráfico de Vendas Mensais */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Vendas Mensais</h2>
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={metrics.monthly_sales}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip formatter={(value) => formatCurrency(Number(value))} />
              <Legend />
              <Line 
                type="monotone" 
                dataKey="value" 
                stroke="#3b82f6" 
                strokeWidth={2}
                name="Vendas"
              />
            </LineChart>
          </ResponsiveContainer>
        </div>

        {/* Gráfico de Oportunidades por Estágio */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Oportunidades por Estágio</h2>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={metrics.opportunities_by_stage}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="stage" />
              <YAxis />
              <Tooltip formatter={(value) => formatCurrency(Number(value))} />
              <Legend />
              <Bar dataKey="value" fill="#10b981" name="Valor Total" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Atividades Recentes */}
      <div className="bg-white rounded-lg shadow p-6">
        <div className="flex items-center gap-2 mb-4">
          <Activity className="w-5 h-5 text-gray-600" />
          <h2 className="text-lg font-semibold text-gray-900">Atividades Recentes</h2>
        </div>
        <div className="space-y-4">
          {activities.map((activity, index) => (
            <div key={index} className="flex items-start gap-4 pb-4 border-b last:border-0">
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
                <p className="text-sm font-medium text-gray-900">{activity.description}</p>
                <p className="text-xs text-gray-500">{activity.user} • {formatDate(activity.created_at)}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
