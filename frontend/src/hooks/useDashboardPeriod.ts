import { useContext } from 'react';
import { DashboardPeriodContext, type DashboardPeriodContextType } from '@/contexts/DashboardPeriodContext';

/**
 * Hook para acessar o contexto de período do dashboard.
 * Deve ser usado dentro de um DashboardPeriodProvider.
 * 
 * @returns Contexto com estado do período mensal (month, year) e métodos para manipulação
 * @throws {Error} Se usado fora do DashboardPeriodProvider
 * 
 * @example
 * ```tsx
 * function MyComponent() {
 *   const { month, year, setPeriod } = useDashboardPeriod();
 *   return <div>{month}/{year}</div>;
 * }
 * ```
 */
export function useDashboardPeriod(): DashboardPeriodContextType {
  const context = useContext(DashboardPeriodContext);
  if (context === undefined) {
    throw new Error('useDashboardPeriod must be used within a DashboardPeriodProvider');
  }
  return context;
}
