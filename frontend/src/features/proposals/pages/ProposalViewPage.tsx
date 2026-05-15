import { useParams, useNavigate } from 'react-router-dom';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft, Edit2, FileDown, Mail, Trash2, AlertCircle } from 'lucide-react';
import proposalService, { type ProposalItem } from '../services/proposalService';
import { Button } from '@/components/ui/button';
import { Alert } from '@/components/common/Alert';
import { useState } from 'react';

/**
 * Status badge color mapping
 */
const STATUS_COLORS: Record<string, string> = {
  draft:    'bg-muted text-muted-foreground',
  sent:     'bg-info/10 text-info',
  approved: 'bg-success/10 text-success',
  rejected: 'bg-destructive/10 text-destructive',
  expired:  'bg-warning/10 text-warning',
};

/**
 * Status labels in Portuguese
 */
const STATUS_LABELS: Record<string, string> = {
  draft: 'Rascunho',
  sent: 'Enviada',
  approved: 'Aprovada',
  rejected: 'Rejeitada',
  expired: 'Expirada',
};

/**
 * Proposal View Page Component
 * Displays detailed view of a single proposal
 */
export default function ProposalViewPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const [alert, setAlert] = useState<{ type: 'success' | 'error'; message: string } | null>(null);
  const [deleteConfirm, setDeleteConfirm] = useState(false);

  // Fetch proposal details
  const { data: proposal, isLoading, error } = useQuery({
    queryKey: ['proposal', id],
    queryFn: () => proposalService.getById(Number(id)),
    enabled: !!id,
  });

  // Delete mutation
  const deleteMutation = useMutation({
    mutationFn: () => proposalService.delete(Number(id)),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['proposals'] });
      navigate('/proposals');
    },
    onError: () => {
      setAlert({ type: 'error', message: 'Erro ao excluir proposta. Tente novamente.' });
    },
  });

  // Format currency
  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL',
    }).format(value);
  };

  // Format date
  const formatDate = (dateString: string | null | undefined) => {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return 'Data inválida';
    
    return new Intl.DateTimeFormat('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    }).format(date);
  };

  // Handle delete
  const handleDelete = () => {
    if (deleteConfirm) {
      deleteMutation.mutate();
    }
  };

  // Handle PDF download
  const handleDownloadPDF = async () => {
    try {
      await proposalService.downloadPdf(proposal.id);
      setAlert({ type: 'success', message: 'PDF baixado com sucesso!' });
    } catch (error) {
      setAlert({ type: 'error', message: 'Erro ao baixar PDF. Tente novamente.' });
    }
  };

  // Handle email send
  const handleSendEmail = async () => {
    try {
      await proposalService.sendEmail(proposal.id);
      setAlert({ type: 'success', message: `Proposta enviada para ${proposal.customer.email}!` });
    } catch (error) {
      setAlert({ type: 'error', message: 'Erro ao enviar email. Tente novamente.' });
    }
  };

  // Calculate subtotal from items
  const calculateSubtotal = () => {
    if (!proposal?.items) return 0;
    return proposal.items.reduce((sum: number, item: ProposalItem) => sum + (item.quantity * item.unit_price), 0);
  };

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-muted-foreground">Carregando proposta...</div>
      </div>
    );
  }

  if (error || !proposal) {
    return (
      <div className="p-6">
        <Alert type="error">
          Erro ao carregar proposta. Tente novamente.
        </Alert>
        <Button
          variant="secondary"
          className="mt-4"
          onClick={() => navigate('/proposals')}
        >
          <ArrowLeft className="w-4 h-4 mr-2" />
          Voltar para Lista
        </Button>
      </div>
    );
  }

  return (
    <div className="p-6 space-y-6">
      {/* Alert */}
      {alert && (
        <Alert type={alert.type} onClose={() => setAlert(null)}>
          {alert.message}
        </Alert>
      )}

      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Button
            variant="secondary"
            onClick={() => navigate('/proposals')}
          >
            <ArrowLeft className="w-4 h-4 mr-2" />
            Voltar
          </Button>
          <div>
            <h1 className="text-2xl font-bold text-foreground">
              Proposta #{proposal.id}
            </h1>
            <p className="text-sm text-muted-foreground mt-1">
              Criada por {proposal.creator.name} em {formatDate(proposal.created_at)}
            </p>
          </div>
        </div>

        <div className="flex items-center gap-2">
          <span
            className={`px-3 py-1 rounded-full text-sm font-medium ${
              STATUS_COLORS[proposal.status]
            }`}
          >
            {STATUS_LABELS[proposal.status]}
          </span>
          {proposal.is_expired && (
            <span className="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
              Expirada
            </span>
          )}
        </div>
      </div>

      {/* Action Buttons */}
      <div className="flex gap-2">
        <Button
          variant="primary"
          onClick={() => navigate(`/proposals/${id}/edit`)}
          disabled={!proposal.can_be_edited}
        >
          <Edit2 className="w-4 h-4 mr-2" />
          Editar
        </Button>
        <Button variant="secondary" onClick={handleDownloadPDF}>
          <FileDown className="w-4 h-4 mr-2" />
          Baixar PDF
        </Button>
        <Button variant="secondary" onClick={handleSendEmail}>
          <Mail className="w-4 h-4 mr-2" />
          Enviar por Email
        </Button>
        <Button
          variant="danger"
          onClick={() => setDeleteConfirm(true)}
          disabled={!proposal.can_be_edited}
        >
          <Trash2 className="w-4 h-4 mr-2" />
          Excluir
        </Button>
      </div>

      {/* Expired Warning */}
      {proposal.is_expired && (
        <Alert type="error">
          <AlertCircle className="w-4 h-4" />
          Esta proposta expirou em {formatDate(proposal.expiration_date)}.
        </Alert>
      )}

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left Column - Details */}
        <div className="lg:col-span-2 space-y-6">
          {/* Customer Information */}
          <div className="bg-card rounded-lg border border-border p-6">
            <h2 className="text-lg font-semibold text-foreground mb-4">
              Informações do Cliente
            </h2>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="text-sm font-medium text-muted-foreground">Nome</label>
                <p className="mt-1 text-foreground">{proposal.customer.name}</p>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Documento</label>
                <p className="mt-1 text-foreground">{proposal.customer.document}</p>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Email</label>
                <p className="mt-1 text-foreground">{proposal.customer.email}</p>
              </div>
            </div>
          </div>

          {/* Items Table */}
          <div className="bg-card rounded-lg border border-border overflow-hidden">
            <div className="p-6 border-b border-border">
              <h2 className="text-lg font-semibold text-foreground">Itens da Proposta</h2>
            </div>
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-border">
                <thead className="bg-muted/50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">
                      Produto
                    </th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase">
                      Qtd
                    </th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase">
                      Preço Unit.
                    </th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase">
                      Desconto
                    </th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase">
                      Total
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-card divide-y divide-border">
                  {proposal.items.map((item: ProposalItem) => (
                      <tr key={item.id}>
                        <td className="px-6 py-4">
                          <div>
                            <div className="text-sm font-medium text-foreground">
                              {item.product?.name || item.description}
                            </div>
                            {item.product?.sku && (
                              <div className="text-sm text-muted-foreground">
                                SKU: {item.product.sku}
                              </div>
                            )}
                          {item.description && item.product?.name !== item.description && (
                            <div className="text-sm text-muted-foreground mt-1">
                              {item.description}
                            </div>
                          )}
                        </div>
                      </td>
                      <td className="px-6 py-4 text-right text-sm text-foreground">
                        {item.quantity}
                      </td>
                      <td className="px-6 py-4 text-right text-sm text-foreground">
                        {formatCurrency(item.unit_price)}
                      </td>
                      <td className="px-6 py-4 text-right text-sm text-foreground">
                        {item.discount_percentage > 0
                          ? `${item.discount_percentage}%`
                          : '-'}
                      </td>
                      <td className="px-6 py-4 text-right text-sm font-medium text-foreground">
                        {formatCurrency(item.total)}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

          {/* Notes */}
          {proposal.notes && (
            <div className="bg-card rounded-lg border border-border p-6">
              <h2 className="text-lg font-semibold text-foreground mb-4">
                Observações
              </h2>
              <p className="text-foreground whitespace-pre-wrap">{proposal.notes}</p>
            </div>
          )}
        </div>

        {/* Right Column - Summary */}
        <div className="space-y-6">
          {/* Dates */}
          <div className="bg-card rounded-lg border border-border p-6">
            <h2 className="text-lg font-semibold text-foreground mb-4">Datas</h2>
            <div className="space-y-4">
              <div>
                <label className="text-sm font-medium text-muted-foreground">Emissão</label>
                <p className="mt-1 text-foreground">{formatDate(proposal.issue_date)}</p>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Validade</label>
                <p className={`mt-1 ${proposal.is_expired ? 'text-destructive font-medium' : 'text-foreground'}`}>
                  {formatDate(proposal.expiration_date)}
                </p>
              </div>
            </div>
          </div>

          {/* Financial Summary */}
          <div className="bg-card rounded-lg border border-border p-6">
            <h2 className="text-lg font-semibold text-foreground mb-4">
              Resumo Financeiro
            </h2>
            <div className="space-y-3">
              <div className="flex justify-between">
                <span className="text-sm text-muted-foreground">Subtotal</span>
                <span className="text-sm font-medium text-foreground">
                  {formatCurrency(calculateSubtotal())}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm text-muted-foreground">Desconto</span>
                <span className="text-sm font-medium text-red-600">
                  - {formatCurrency(proposal.discount)}
                </span>
              </div>
              <div className="pt-3 border-t border-border">
                <div className="flex justify-between">
                  <span className="text-base font-semibold text-foreground">Total</span>
                  <span className="text-xl font-bold text-blue-600">
                    {formatCurrency(proposal.total)}
                  </span>
                </div>
              </div>
            </div>
          </div>

          {/* Metadata */}
          <div className="bg-muted/50 rounded-lg border border-border p-6">
            <h2 className="text-sm font-semibold text-muted-foreground mb-3">
              Informações do Sistema
            </h2>
            <div className="space-y-2 text-xs text-muted-foreground">
              <div className="flex justify-between">
                <span>Criado em:</span>
                <span>{formatDate(proposal.created_at)}</span>
              </div>
              <div className="flex justify-between">
                <span>Atualizado em:</span>
                <span>{formatDate(proposal.updated_at)}</span>
              </div>
              <div className="flex justify-between">
                <span>Criado por:</span>
                <span>{proposal.creator.name}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Delete Confirmation Modal */}
      {deleteConfirm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">
              Confirmar Exclusão
            </h3>
            <p className="text-gray-600 mb-6">
              Tem certeza que deseja excluir a proposta <strong>{proposal.number}</strong>?
              Esta ação não pode ser desfeita.
            </p>
            <div className="flex gap-3 justify-end">
              <Button
                variant="secondary"
                onClick={() => setDeleteConfirm(false)}
              >
                Cancelar
              </Button>
              <Button
                variant="danger"
                onClick={handleDelete}
                disabled={deleteMutation.isPending}
              >
                {deleteMutation.isPending ? 'Excluindo...' : 'Excluir'}
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
