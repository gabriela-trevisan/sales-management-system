import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { PieChart, Pie, Cell, ResponsiveContainer, Legend, Tooltip } from 'recharts';
import { useDashboardPeriod } from '@/hooks/useDashboardPeriod';
import { useCustomersBySegment } from '@/features/dashboard/hooks/useDashboard';

const CHART_COLORS = [
  'var(--color-chart-1)',
  'var(--color-chart-2)',
  'var(--color-chart-3)',
  'var(--color-chart-4)',
  'var(--color-chart-5)',
];

export function CustomersBySegmentChart() {
  const { month, year } = useDashboardPeriod();
  const { data, isLoading, isError } = useCustomersBySegment(month, year);

  if (isLoading) {
    return (
      <Card>
        <CardHeader>
          <CardTitle className="text-base">Clientes por Segmento</CardTitle>
        </CardHeader>
        <CardContent className="flex items-center justify-center h-[300px]">
          <Skeleton className="w-48 h-48 rounded-full" />
        </CardContent>
      </Card>
    );
  }

  if (isError || !data || data.length === 0) {
    return (
      <Card>
        <CardHeader>
          <CardTitle className="text-base">Clientes por Segmento</CardTitle>
        </CardHeader>
        <CardContent className="flex items-center justify-center h-[300px]">
          <div className="text-muted-foreground text-sm">
            {isError ? 'Erro ao carregar dados' : 'Nenhum dado disponível'}
          </div>
        </CardContent>
      </Card>
    );
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle className="text-base">Clientes por Segmento</CardTitle>
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
                backgroundColor: 'var(--color-card)',
                border: '1px solid var(--color-border)',
                borderRadius: '8px',
                color: 'var(--color-foreground)',
              }}
              formatter={(value, _name, props) => {
                const percentage = (props.payload as { percentage?: number } | undefined)?.percentage ?? 0;
                return [`${value ?? 0} clientes (${percentage}%)`, ''];
              }}
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
