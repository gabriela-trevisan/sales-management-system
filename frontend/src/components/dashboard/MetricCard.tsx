import { TrendingUp, TrendingDown, type LucideIcon } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { NumericFormat } from 'react-number-format';
import { useEffect, useState, useMemo } from 'react';

interface MetricCardProps {
  title: string;
  value: number;
  trend?: number;
  icon: LucideIcon;
  iconColor?: string;
  formatValue?: 'number' | 'currency' | 'percentage';
  loading?: boolean;
}

export function MetricCard({
  title,
  value,
  trend,
  icon: Icon,
  iconColor = 'text-primary',
  formatValue = 'number',
  loading = false,
}: MetricCardProps) {
  const [displayValue, setDisplayValue] = useState(0);

  useEffect(() => {
    if (!loading) {
      const duration = 1000; // 1 segundo
      const steps = 60;
      const increment = value / steps;
      let current = 0;

      const timer = setInterval(() => {
        current += increment;
        if (current >= value) {
          setDisplayValue(value);
          clearInterval(timer);
        } else {
          setDisplayValue(Math.floor(current));
        }
      }, duration / steps);

      return () => clearInterval(timer);
    }
  }, [value, loading]);

  const trendData = useMemo(() => {
    if (trend === undefined || trend === 0) {
      return { icon: null, color: '' };
    }
    return {
      icon: trend > 0 ? TrendingUp : TrendingDown,
      color: trend > 0 ? 'text-success' : 'text-destructive',
    };
  }, [trend]);

  const getFormattedValue = () => {
    if (loading) return '-';

    switch (formatValue) {
      case 'currency':
        return (
          <NumericFormat
            value={displayValue}
            displayType="text"
            thousandSeparator="."
            decimalSeparator=","
            prefix="R$ "
            decimalScale={2}
            fixedDecimalScale
          />
        );
      case 'percentage':
        return (
          <NumericFormat
            value={displayValue}
            displayType="text"
            suffix="%"
            decimalScale={1}
            fixedDecimalScale
          />
        );
      default:
        return (
          <NumericFormat
            value={displayValue}
            displayType="text"
            thousandSeparator="."
            decimalSeparator=","
          />
        );
    }
  };

  return (
    <Card className="hover:shadow-md transition-shadow">
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <CardTitle className="text-sm font-medium text-muted-foreground">
          {title}
        </CardTitle>
        <Icon className={cn('h-5 w-5', iconColor)} />
      </CardHeader>
      <CardContent>
        <div className="space-y-1">
          <div className="text-2xl font-bold text-foreground">
            {getFormattedValue()}
          </div>
          {trend !== undefined && trend !== 0 && trendData.icon && (
            <div className={cn('flex items-center gap-1 text-xs', trendData.color)}>
              <trendData.icon className="h-3 w-3" />
              <NumericFormat
                value={Math.abs(trend)}
                displayType="text"
                suffix="%"
                decimalScale={1}
                fixedDecimalScale
              />
              <span className="text-muted-foreground">vs mês anterior</span>
            </div>
          )}
        </div>
      </CardContent>
    </Card>
  );
}
