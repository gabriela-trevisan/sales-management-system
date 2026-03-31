import { Calendar } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { useDashboardPeriod } from '@/hooks/useDashboardPeriod';
import { useState } from 'react';

const MONTHS = [
  { value: '01', label: 'Janeiro' },
  { value: '02', label: 'Fevereiro' },
  { value: '03', label: 'Março' },
  { value: '04', label: 'Abril' },
  { value: '05', label: 'Maio' },
  { value: '06', label: 'Junho' },
  { value: '07', label: 'Julho' },
  { value: '08', label: 'Agosto' },
  { value: '09', label: 'Setembro' },
  { value: '10', label: 'Outubro' },
  { value: '11', label: 'Novembro' },
  { value: '12', label: 'Dezembro' },
];

export function MonthlyPeriodSelector() {
  const { month, year, setPeriod, getFormattedPeriod } = useDashboardPeriod();
  const [open, setOpen] = useState(false);
  const [tempMonth, setTempMonth] = useState(month);
  const [tempYear, setTempYear] = useState(year);

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: 5 }, (_, i) => (currentYear - 2 + i).toString());

  const handleApply = () => {
    setPeriod(tempMonth, tempYear);
    setOpen(false);
  };

  const handleCancel = () => {
    setTempMonth(month);
    setTempYear(year);
    setOpen(false);
  };

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger asChild>
        <Button
          variant="outline"
          className={cn(
            'justify-start text-left font-normal',
            'border-oklch-outline/50 hover:border-oklch-outline',
            'bg-oklch-surface hover:bg-oklch-surface-container-high'
          )}
        >
          <Calendar className="mr-2 h-4 w-4" />
          {getFormattedPeriod()}
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-80 bg-oklch-surface-container border-oklch-outline-variant" align="start">
        <div className="space-y-4">
          <div className="space-y-2">
            <h4 className="font-medium text-sm text-oklch-on-surface">Selecione o mês</h4>
            <div className="grid grid-cols-3 gap-2">
              {MONTHS.map((m) => (
                <Button
                  key={m.value}
                  variant={tempMonth === m.value ? 'default' : 'outline'}
                  size="sm"
                  className={cn(
                    'h-8',
                    tempMonth === m.value
                      ? 'bg-oklch-primary text-oklch-on-primary hover:bg-oklch-primary/90'
                      : 'bg-oklch-surface hover:bg-oklch-surface-container-high border-oklch-outline-variant'
                  )}
                  onClick={() => setTempMonth(m.value)}
                >
                  {m.label.slice(0, 3)}
                </Button>
              ))}
            </div>
          </div>

          <div className="space-y-2">
            <h4 className="font-medium text-sm text-oklch-on-surface">Selecione o ano</h4>
            <div className="grid grid-cols-5 gap-2">
              {years.map((y) => (
                <Button
                  key={y}
                  variant={tempYear === y ? 'default' : 'outline'}
                  size="sm"
                  className={cn(
                    'h-8',
                    tempYear === y
                      ? 'bg-oklch-primary text-oklch-on-primary hover:bg-oklch-primary/90'
                      : 'bg-oklch-surface hover:bg-oklch-surface-container-high border-oklch-outline-variant'
                  )}
                  onClick={() => setTempYear(y)}
                >
                  {y}
                </Button>
              ))}
            </div>
          </div>

          <div className="flex justify-end gap-2 pt-2">
            <Button
              variant="outline"
              size="sm"
              onClick={handleCancel}
              className="bg-oklch-surface hover:bg-oklch-surface-container-high border-oklch-outline-variant"
            >
              Cancelar
            </Button>
            <Button
              size="sm"
              onClick={handleApply}
              className="bg-oklch-primary text-oklch-on-primary hover:bg-oklch-primary/90"
            >
              Aplicar
            </Button>
          </div>
        </div>
      </PopoverContent>
    </Popover>
  );
}
