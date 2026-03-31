import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { PieChart, Pie, Cell, ResponsiveContainer, Legend, Tooltip } from 'recharts';
import { useDashboardPeriod } from '@/hooks/useDashboardPeriod';
import { useEffect, useState } from 'react';
import api from '@/services/api';

interface SegmentData {
  name: string;
  count: number;
  percentage: number;
}

const CHART_COLORS = [
  'oklch(var(--chart-1))',
  'oklch(var(--chart-2))',
  'oklch(var(--chart-3))',
  'oklch(var(--chart-4))',
  'oklch(var(--chart-5))',
];

export function CustomersBySegmentChart() {
  const { getApiParams } = useDashboardPeriod();
  const [data, setData] = useState<SegmentData[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const params = getApiParams();
        const response = await api.get('/api/dashboard/customers-by-segment', { params });
        setData(response.data.segments || []);
      } catch (error) {
        console.error('Erro ao carregar dados de segmentos:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [getApiParams]);

  if (loading) {
    return (
      <Card className="bg-oklch-surface-container border-oklch-outline-variant">
        <CardHeader>
          <CardTitle className="text-base text-oklch-on-surface">
            Clientes por Segmento
          </CardTitle>
        </CardHeader>
        <CardContent className="flex items-center justify-center h-[300px]">
          <div className="text-oklch-on-surface-variant">Carregando...</div>
        </CardContent>
      </Card>
    );
  }

  if (!data || data.length === 0) {
    return (
      <Card className="bg-oklch-surface-container border-oklch-outline-variant">
        <CardHeader>
          <CardTitle className="text-base text-oklch-on-surface">
            Clientes por Segmento
          </CardTitle>
        </CardHeader>
        <CardContent className="flex items-center justify-center h-[300px]">
          <div className="text-oklch-on-surface-variant">Nenhum dado disponível</div>
        </CardContent>
      </Card>
    );
  }

  return (
    <Card className="bg-oklch-surface-container border-oklch-outline-variant">
      <CardHeader>
        <CardTitle className="text-base text-oklch-on-surface">
          Clientes por Segmento
        </CardTitle>
      </CardHeader>
      <CardContent>
        <ResponsiveContainer width="100%" height={300}>
          <PieChart>
            <Pie
              data={data}
              cx="50%"
              cy="50%"
              outerRadius={100}
              innerRadius={60}
              fill="#8884d8"
              dataKey="count"
              animationBegin={0}
              animationDuration={800}
            >
              {data.map((_entry, index) => (
                <Cell
                  key={`cell-${index}`}
                  fill={CHART_COLORS[index % CHART_COLORS.length]}
                />
              ))}
            </Pie>
            <Tooltip
              contentStyle={{
                backgroundColor: 'oklch(var(--surface-container))',
                border: '1px solid oklch(var(--outline-variant))',
                borderRadius: '8px',
              }}
              formatter={(value: number | undefined) => [
                value ? `${value} clientes` : '',
                '',
              ]}
            />
            <Legend
              verticalAlign="bottom"
              height={36}
              iconType="circle"
            />
          </PieChart>
        </ResponsiveContainer>
      </CardContent>
    </Card>
  );
}
