import type { ReactNode } from 'react';

interface AlertProps {
  type?: 'success' | 'error' | 'warning' | 'info';
  children: ReactNode;
  onClose?: () => void;
}

export function Alert({ type = 'info', children, onClose }: AlertProps) {
  const styles = {
    success: 'bg-success/10 border-success text-foreground',
    error:   'bg-destructive/10 border-destructive text-foreground',
    warning: 'bg-warning/10 border-warning text-foreground',
    info:    'bg-info/10 border-info text-foreground',
  };

  return (
    <div className={`border-l-4 p-4 rounded-r-md ${styles[type]} flex items-start justify-between`}>
      <div className="flex-1">{children}</div>
      {onClose && (
        <button
          onClick={onClose}
          className="ml-4 text-muted-foreground hover:text-foreground transition-colors"
        >
          <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
          </svg>
        </button>
      )}
    </div>
  );
}
