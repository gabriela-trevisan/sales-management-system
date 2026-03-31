import { AlertTriangle, X } from 'lucide-react';

interface ConfirmDialogProps {
  isOpen: boolean;
  title: string;
  message: string;
  confirmLabel?: string;
  cancelLabel?: string;
  onConfirm: () => void;
  onCancel: () => void;
  variant?: 'danger' | 'warning' | 'info';
}

/**
 * Modal de confirmação moderno e acessível.
 * Usa overlay com backdrop blur e animações suaves.
 * 
 * @example
 * <ConfirmDialog
 *   isOpen={showDialog}
 *   title="Excluir cliente"
 *   message="Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita."
 *   confirmLabel="Excluir"
 *   cancelLabel="Cancelar"
 *   onConfirm={handleConfirm}
 *   onCancel={() => setShowDialog(false)}
 *   variant="danger"
 * />
 */
const ConfirmDialog = ({
  isOpen,
  title,
  message,
  confirmLabel = 'Confirmar',
  cancelLabel = 'Cancelar',
  onConfirm,
  onCancel,
  variant = 'danger',
}: ConfirmDialogProps) => {
  if (!isOpen) return null;

  const variants = {
    danger: {
      icon: 'bg-destructive/10 text-destructive',
      button: 'bg-destructive hover:bg-destructive/90 focus:ring-destructive',
    },
    warning: {
      icon: 'bg-warning/10 text-warning',
      button: 'bg-warning hover:bg-warning/90 focus:ring-warning text-warning-foreground',
    },
    info: {
      icon: 'bg-primary/10 text-primary',
      button: 'bg-primary hover:bg-primary/90 focus:ring-primary text-primary-foreground',
    },
  };

  const variantStyles = variants[variant];

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto">
      {/* Backdrop */}
      <div
        className="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
        onClick={onCancel}
      />

      {/* Dialog */}
      <div className="flex min-h-full items-center justify-center p-4">
        <div className="relative bg-card border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">
          {/* Close button */}
          <button
            onClick={onCancel}
            className="absolute top-4 right-4 text-muted-foreground hover:text-foreground transition-colors"
          >
            <X size={20} />
          </button>

          {/* Icon */}
          <div className={`mx-auto flex items-center justify-center h-12 w-12 rounded-full ${variantStyles.icon} mb-4`}>
            <AlertTriangle size={24} />
          </div>

          {/* Content */}
          <div className="text-center">
            <h3 className="text-lg font-semibold text-card-foreground mb-2">
              {title}
            </h3>
            <p className="text-sm text-muted-foreground mb-6">
              {message}
            </p>
          </div>

          {/* Actions */}
          <div className="flex gap-3">
            <button
              onClick={onCancel}
              className="flex-1 px-4 py-2 border border-input rounded-lg text-foreground hover:bg-accent transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring"
            >
              {cancelLabel}
            </button>
            <button
              onClick={onConfirm}
              className={`flex-1 px-4 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 ${variantStyles.button}`}
            >
              {confirmLabel}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ConfirmDialog;
