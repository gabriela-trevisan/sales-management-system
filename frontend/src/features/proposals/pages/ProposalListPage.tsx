import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Search, FileText, Trash2, Edit2, Eye } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import proposalService, { type Proposal, type ProposalFilters, type CreateProposalData, type UpdateProposalData } from '../services/proposalService';
import customerService from '@/features/customers/services/customerService';
import productService from '@/features/products/services/productService';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Alert } from '@/components/common/Alert';
import ProposalFormModal from '../components/ProposalFormModal';
import { type ProposalFormData } from '../schemas/proposalSchema';

/**
 * Status badge color mapping
 */
const STATUS_COLORS: Record<Proposal['status'], string> = {
  draft:    'bg-muted text-muted-foreground',
  sent:     'bg-info/10 text-info',
  approved: 'bg-success/10 text-success',
  rejected: 'bg-destructive/10 text-destructive',
  expired:  'bg-warning/10 text-warning',
};

/**
 * Status labels in Portuguese
 */
const STATUS_LABELS: Record<Proposal['status'], string> = {
  draft: 'Rascunho',
  sent: 'Enviada',
  approved: 'Aprovada',
  rejected: 'Rejeitada',
  expired: 'Expirada',
};

/**
 * Proposals List Page Component
 */
export default function ProposalListPage() {
  const queryClient = useQueryClient();
  const navigate = useNavigate();
  const [filters, setFilters] = useState<ProposalFilters>({ per_page: 15 });
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedStatus, setSelectedStatus] = useState<string>('');
  const [deleteConfirm, setDeleteConfirm] = useState<{ isOpen: boolean; proposalId: number | null }>({
    isOpen: false,
    proposalId: null,
  });
  const [alert, setAlert] = useState<{ type: 'success' | 'error'; message: string } | null>(null);
  const [modalState, setModalState] = useState<{
    isOpen: boolean;
    proposal: Proposal | null;
  }>({
    isOpen: false,
    proposal: null,
  });

  const { data, isLoading, error } = useQuery({
    queryKey: ['proposals', filters],
    queryFn: () => proposalService.getAll(filters),
  });

  const { data: customersData, isLoading: loadingCustomers } = useQuery({
    queryKey: ['customers', { per_page: 1000 }],
    queryFn: () => customerService.getAll({ per_page: 1000 }),
  });

  const { data: productsData, isLoading: loadingProducts } = useQuery({
    queryKey: ['products', { is_active: true, per_page: 1000 }],
    queryFn: () => productService.getAll({ is_active: true, per_page: 1000 }),
  });

  const createMutation = useMutation({
    mutationFn: (data: CreateProposalData) => proposalService.create(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['proposals'] });
      setAlert({ type: 'success', message: 'Proposta criada com sucesso!' });
      setModalState({ isOpen: false, proposal: null });
    },
    onError: () => {
      setAlert({ type: 'error', message: 'Erro ao criar proposta. Tente novamente.' });
    },
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: UpdateProposalData }) =>
      proposalService.update(id, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['proposals'] });
      setAlert({ type: 'success', message: 'Proposta atualizada com sucesso!' });
      setModalState({ isOpen: false, proposal: null });
    },
    onError: () => {
      setAlert({ type: 'error', message: 'Erro ao atualizar proposta. Tente novamente.' });
    },
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => proposalService.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['proposals'] });
      setAlert({ type: 'success', message: 'Proposta excluída com sucesso!' });
      setDeleteConfirm({ isOpen: false, proposalId: null });
    },
    onError: () => {
      setAlert({ type: 'error', message: 'Erro ao excluir proposta. Tente novamente.' });
    },
  });

  const handleSearch = () => {
    setFilters({ ...filters, search: searchTerm, status: selectedStatus || undefined });
  };

  const handleCreate = () => {
    setModalState({ isOpen: true, proposal: null });
  };

  const handleEdit = (proposal: Proposal) => {
    setModalState({ isOpen: true, proposal });
  };

  const handleFormSubmit = (data: ProposalFormData) => {
    if (modalState.proposal) {
      updateMutation.mutate({ id: modalState.proposal.id, data });
    } else {
      createMutation.mutate(data as CreateProposalData);
    }
  };

  const handleDelete = (id: number) => {
    setDeleteConfirm({ isOpen: true, proposalId: id });
  };

  const confirmDelete = () => {
    if (deleteConfirm.proposalId) {
      deleteMutation.mutate(deleteConfirm.proposalId);
    }
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
  };

  const formatDate = (date: string | null | undefined) => {
    if (!date) return 'N/A';
    
    const dateObj = new Date(date);
    if (isNaN(dateObj.getTime())) return 'Data inválida';
    
    return dateObj.toLocaleDateString('pt-BR');
  };

  return (
    <div className="p-6">
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Propostas Comerciais</h1>
          <p className="text-gray-600">Gerencie propostas enviadas aos clientes</p>
        </div>
        <Button onClick={handleCreate} className="flex items-center gap-2">
          <Plus size={20} />
          Nova Proposta
        </Button>
      </div>

      {alert && (
        <Alert
          type={alert.type}
          onClose={() => setAlert(null)}
        >
          {alert.message}
        </Alert>
      )}

      {/* Filtros */}
      <div className="mb-6 flex gap-4">
        <div className="flex-1">
          <Input
            type="text"
            placeholder="Buscar por número, cliente ou notas..."
            value={searchTerm}
            onChange={(e: React.ChangeEvent<HTMLInputElement>) => setSearchTerm(e.target.value)}
            onKeyDown={(e: React.KeyboardEvent<HTMLInputElement>) => e.key === 'Enter' && handleSearch()}
          />
        </div>
        <select
          value={selectedStatus}
          onChange={(e) => setSelectedStatus(e.target.value)}
          className="rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none"
        >
          <option value="">Todos os status</option>
          <option value="draft">Rascunho</option>
          <option value="sent">Enviada</option>
          <option value="approved">Aprovada</option>
          <option value="rejected">Rejeitada</option>
          <option value="expired">Expirada</option>
        </select>
        <Button onClick={handleSearch} className="flex items-center gap-2">
          <Search size={18} />
          Buscar
        </Button>
      </div>

      {/* Tabela */}
      {isLoading ? (
        <div className="flex h-64 items-center justify-center">
          <div className="h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
        </div>
      ) : error ? (
        <Alert type="error">Erro ao carregar propostas. Tente novamente.</Alert>
      ) : (
        <div className="overflow-hidden rounded-lg bg-white shadow">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-muted/50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Número
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Cliente
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Data Emissão
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Validade
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Valor Total
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Status
                </th>
                <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                  Ações
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border bg-card">
              {data?.data?.length === 0 ? (
                <tr>
                  <td colSpan={7} className="px-6 py-8 text-center text-gray-500">
                    <FileText className="mx-auto mb-2 h-12 w-12 text-gray-400" />
                    <p>Nenhuma proposta encontrada</p>
                  </td>
                </tr>
              ) : (
                data?.data?.map((proposal: Proposal) => (
                  <tr key={proposal.id} className="hover:bg-muted/50 transition-colors">
                    <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                      {proposal.number}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">
                      {proposal.customer.name}
                    </td>
                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                      {formatDate(proposal.issue_date)}
                    </td>
                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                      {formatDate(proposal.expiration_date)}
                    </td>
                    <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                      {formatCurrency(proposal.total)}
                    </td>
                    <td className="whitespace-nowrap px-6 py-4 text-sm">
                      <span className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${STATUS_COLORS[proposal.status]}`}>
                        {STATUS_LABELS[proposal.status]}
                      </span>
                    </td>
                    <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                      <div className="flex justify-end gap-2">
                        <button
                          onClick={() => navigate(`/proposals/${proposal.id}`)}
                          className="text-blue-600 hover:text-blue-900"
                          title="Visualizar"
                        >
                          <Eye size={18} />
                        </button>
                        {proposal.can_be_edited && (
                          <button
                            onClick={() => handleEdit(proposal)}
                            className="text-muted-foreground hover:text-foreground transition-colors"
                            title="Editar"
                          >
                            <Edit2 size={18} />
                          </button>
                        )}
                        <button
                          onClick={() => handleDelete(proposal.id)}
                          className="text-red-600 hover:text-red-900"
                          title="Excluir"
                        >
                          <Trash2 size={18} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      )}

      {/* TODO(#?): substituir inline dialog pelo componente ConfirmDialog já existente */}
      {deleteConfirm.isOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
          <div className="rounded-lg bg-white p-6 shadow-xl">
            <h3 className="mb-4 text-lg font-semibold">Excluir Proposta</h3>
            <p className="mb-6 text-gray-600">
              Tem certeza que deseja excluir esta proposta? Esta ação não pode ser desfeita.
            </p>
            <div className="flex justify-end gap-3">
              <Button
                variant="secondary"
                onClick={() => setDeleteConfirm({ isOpen: false, proposalId: null })}
              >
                Cancelar
              </Button>
              <Button variant="danger" onClick={confirmDelete}>
                Excluir
              </Button>
            </div>
          </div>
        </div>
      )}

      {/* Proposal Form Modal */}
      <ProposalFormModal
        isOpen={modalState.isOpen}
        onClose={() => setModalState({ isOpen: false, proposal: null })}
        onSubmit={handleFormSubmit}
        proposal={modalState.proposal}
        isLoading={createMutation.isPending || updateMutation.isPending}
        customers={customersData?.data || []}
        products={productsData?.data || []}
        loadingCustomers={loadingCustomers}
        loadingProducts={loadingProducts}
      />
    </div>
  );
}
