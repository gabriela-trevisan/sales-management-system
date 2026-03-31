import { createContext, useState, type ReactNode } from 'react';
import { format } from 'date-fns';

export interface DashboardPeriodContextType {
  month: string;
  year: string;
  setMonth: (month: string) => void;
  setYear: (year: string) => void;
  setPeriod: (month: string, year: string) => void;
  getFormattedPeriod: () => string;
  getApiParams: () => { month: string; year: string };
}

const DashboardPeriodContext = createContext<DashboardPeriodContextType | undefined>(undefined);

interface DashboardPeriodProviderProps {
  children: ReactNode;
}

export function DashboardPeriodProvider({ children }: DashboardPeriodProviderProps) {
  const now = new Date();
  const [month, setMonth] = useState(format(now, 'MM'));
  const [year, setYear] = useState(format(now, 'yyyy'));

  const setPeriod = (newMonth: string, newYear: string) => {
    setMonth(newMonth);
    setYear(newYear);
  };

  const getFormattedPeriod = () => {
    return `${month}/${year}`;
  };

  const getApiParams = () => {
    return { month, year };
  };

  return (
    <DashboardPeriodContext.Provider
      value={{
        month,
        year,
        setMonth,
        setYear,
        setPeriod,
        getFormattedPeriod,
        getApiParams,
      }}
    >
      {children}
    </DashboardPeriodContext.Provider>
  );
}

export { DashboardPeriodContext };
