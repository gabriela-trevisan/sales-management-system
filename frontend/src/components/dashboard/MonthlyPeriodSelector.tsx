import { Calendar } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
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
          className="justify-start text-left font-normal"
        >
          <Calendar className="mr-2 h-4 w-4" />
          {getFormattedPeriod()}
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-80" align="start">
        <div className="space-y-4">
          <div className="space-y-2">
            <h4 className="font-medium text-sm text-foreground">Selecione o mês</h4>
            <div className="grid grid-cols-3 gap-2">
              {MONTHS.map((m) => (
                <Button
                  key={m.value}
                  variant={tempMonth === m.value ? 'default' : 'outline'}
                  size="sm"
                  className="h-8"
                  onClick={() => setTempMonth(m.value)}
                >
                  {m.label.slice(0, 3)}
                </Button>
              ))}
            </div>
          </div>

          <div className="space-y-2">
            <h4 className="font-medium text-sm text-foreground">Selecione o ano</h4>
            <div className="grid grid-cols-5 gap-2">
              {years.map((y) => (
                <Button
                  key={y}
                  variant={tempYear === y ? 'default' : 'outline'}
                  size="sm"
                  className="h-8"
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
            >
              Cancelar
            </Button>
            <Button
              size="sm"
              onClick={handleApply}
            >
              Aplicar
            </Button>
          </div>
        </div>
      </PopoverContent>
    </Popover>
  );
}
