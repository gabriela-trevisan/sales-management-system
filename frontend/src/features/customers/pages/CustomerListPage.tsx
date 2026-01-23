import { useState, useEffect } from 'react';
import { Plus, Search, Edit, Trash2, UserCircle } from 'lucide-react';
import customerService, { type Customer, type CustomerFilters } from '../services/customerService';
import CustomerFormModal from '../components/CustomerFormModal';
import ConfirmDialog from '@/components/ConfirmDialog';
import { formatDocument, formatPhone } from '@/utils/formatters';

const CustomerListPage = () => {
  const [customers, setCustomers] = useState<Customer[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingCustomer, setEditingCustomer] = useState<Customer | null>(null);
  const [deleteConfirm, setDeleteConfirm] = useState<{ isOpen: boolean; customerId: number | null }>({
    isOpen: false,
    customerId: null,
  });
  const [filters, setFilters] = useState<CustomerFilters>({
    search: '',
    status: '',
    per_page: 15,
  });
  const [currentPage, setCurrentPage] = useState(1);
  const [meta, setMeta] = useState({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  });

  const loadCustomers = async () => {
    try {
      setIsLoading(true);
      const response = await customerService.getAll({ 
        ...filters, 
        per_page: 15,
        page: currentPage 
      });
      setCustomers(response.data);
      setMeta(response.meta);
    } catch (error) {
      console.error('Erro ao carregar clientes:', error);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    loadCustomers();
  }, [filters, currentPage]);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setCurrentPage(1);
    loadCustomers();
  };

  const handleCreate = () => {
    setEditingCustomer(null);
    setIsModalOpen(true);
  };

  const handleEdit = (customer: Customer) => {
    setEditingCustomer(customer);
    setIsModalOpen(true);
  };

  const handleDelete = (id: number) => {
    setDeleteConfirm({ isOpen: true, customerId: id });
  };

  const confirmDelete = async () => {
    if (!deleteConfirm.customerId) return;

    try {
      await customerService.delete(deleteConfirm.customerId);
      setDeleteConfirm({ isOpen: false, customerId: null });
      loadCustomers();
    } catch (error) {
      console.error('Erro ao excluir cliente:', error);
      alert('Erro ao excluir cliente');
    }
  };

  const handleSave = async () => {
    setIsModalOpen(false);
    setEditingCustomer(null);
    loadCustomers();
  };

  const getStatusBadge = (status: string) => {
    const styles = {
      active: 'bg-green-100 text-green-800',
      inactive: 'bg-gray-100 text-gray-800',
      prospect: 'bg-blue-100 text-blue-800',
      churned: 'bg-red-100 text-red-800',
    };

    const labels = {
      active: 'Ativo',
      inactive: 'Inativo',
      prospect: 'Prospecto',
      churned: 'Perdido',
    };

    return (
      <span className={`px-2 py-1 text-xs font-medium rounded-full ${styles[status as keyof typeof styles]}`}>
        {labels[status as keyof typeof labels]}
      </span>
    );
  };

  return (
    <div className="p-6">
      {/* Header */}
      <div className="mb-6">
        <h1 className="text-2xl font-bold text-gray-900 mb-2">Clientes</h1>
        <p className="text-gray-600">Gerencie sua base de clientes e prospects</p>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-lg shadow mb-6 p-4">
        <form onSubmit={handleSearch} className="flex gap-4 items-end">
          <div className="flex-1">
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Buscar
            </label>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
              <input
                type="text"
                placeholder="Nome, email ou documento..."
                value={filters.search}
                onChange={(e) => setFilters({ ...filters, search: e.target.value })}
                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          <div className="w-48">
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Status
            </label>
            <select
              value={filters.status}
              onChange={(e) => setFilters({ ...filters, status: e.target.value })}
              className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Todos</option>
              <option value="active">Ativo</option>
              <option value="inactive">Inativo</option>
              <option value="prospect">Prospecto</option>
              <option value="churned">Perdido</option>
            </select>
          </div>

          <button
            type="button"
            onClick={handleCreate}
            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
          >
            <Plus size={20} />
            Novo Cliente
          </button>
        </form>
      </div>

      {/* Table */}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        {isLoading ? (
          <div className="flex items-center justify-center py-12">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
          </div>
        ) : customers.length === 0 ? (
          <div className="text-center py-12">
            <UserCircle className="mx-auto h-12 w-12 text-gray-400 mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">Nenhum cliente encontrado</h3>
            <p className="text-gray-600 mb-4">Comece criando seu primeiro cliente</p>
            <button
              onClick={handleCreate}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center gap-2"
            >
              <Plus size={20} />
              Novo Cliente
            </button>
          </div>
        ) : (
          <>
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Cliente
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Contato
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Segmento
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Responsável
                  </th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ações
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {customers.map((customer) => (
                  <tr key={customer.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div className="text-sm font-medium text-gray-900">{customer.name}</div>
                        <div className="text-sm text-gray-500">{formatDocument(customer.document)}</div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div className="text-sm text-gray-900">{customer.email}</div>
                        {customer.phone && (
                          <div className="text-sm text-gray-500">{formatPhone(customer.phone)}</div>
                        )}
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {getStatusBadge(customer.status)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {customer.segment?.name || '-'}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {customer.assigned_to?.name || '-'}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div className="flex items-center justify-end gap-2">
                        <button
                          onClick={() => handleEdit(customer)}
                          className="text-blue-600 hover:text-blue-900 p-1"
                          title="Editar"
                        >
                          <Edit size={18} />
                        </button>
                        <button
                          onClick={() => handleDelete(customer.id)}
                          className="text-red-600 hover:text-red-900 p-1"
                          title="Excluir"
                        >
                          <Trash2 size={18} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>

            {/* Pagination */}
            {meta.last_page > 1 && (
              <div className="bg-white px-6 py-4 flex items-center justify-between border-t border-gray-200">
                <div className="text-sm text-gray-700">
                  Mostrando <span className="font-medium">{(meta.current_page - 1) * meta.per_page + 1}</span> até{' '}
                  <span className="font-medium">
                    {Math.min(meta.current_page * meta.per_page, meta.total)}
                  </span>{' '}
                  de <span className="font-medium">{meta.total}</span> resultados
                </div>
                <div className="flex gap-2">
                  {meta.current_page > 1 && (
                    <button
                      onClick={() => setCurrentPage(meta.current_page - 1)}
                      className="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                      Anterior
                    </button>
                  )}
                  {meta.current_page < meta.last_page && (
                    <button
                      onClick={() => setCurrentPage(meta.current_page + 1)}
                      className="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                      Próxima
                    </button>
                  )}
                </div>
              </div>
            )}
          </>
        )}
      </div>

      {/* Modal */}
      {isModalOpen && (
        <CustomerFormModal
          customer={editingCustomer}
          onClose={() => {
            setIsModalOpen(false);
            setEditingCustomer(null);
          }}
          onSave={handleSave}
        />
      )}

      {/* Confirm Dialog */}
      <ConfirmDialog
        isOpen={deleteConfirm.isOpen}
        title="Excluir cliente"
        message="Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita e todos os dados relacionados serão perdidos."
        confirmLabel="Excluir"
        cancelLabel="Cancelar"
        onConfirm={confirmDelete}
        onCancel={() => setDeleteConfirm({ isOpen: false, customerId: null })}
        variant="danger"
      />
    </div>
  );
};

export default CustomerListPage;
