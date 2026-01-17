import api from '@/services/api';

/**
 * Interface do modelo Customer.
 */
export interface Customer {
  id: number;
  name: string;
  document: string;
  email: string;
  phone?: string;
  status: 'active' | 'inactive' | 'prospect' | 'churned';
  rfm_score?: {
    recency: number;
    frequency: number;
    monetary: number;
  };
  segment?: {
    id: number;
    name: string;
  };
  assigned_to?: {
    id: number;
    name: string;
  };
  created_at: string;
  updated_at: string;
}

/**
 * Filtros disponíveis para listagem de clientes.
 */
export interface CustomerFilters {
  status?: string;
  assigned_to?: number;
  search?: string;
  per_page?: number;
  page?: number;
}

/**
 * Resposta paginada da API de clientes.
 */
export interface PaginatedCustomers {
  data: Customer[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

/**
 * Dados para criação/atualização de cliente.
 */
export interface CustomerFormData {
  name: string;
  document: string;
  email: string;
  phone?: string;
  status?: string;
  segment_id?: number;
  assigned_to?: number;
}

/**
 * Serviço para operações CRUD de clientes.
 */
const customerService = {
  /**
   * Lista clientes com filtros e paginação.
   */
  async getAll(filters?: CustomerFilters): Promise<PaginatedCustomers> {
    const params = new URLSearchParams();
    
    if (filters?.status) params.append('status', filters.status);
    if (filters?.assigned_to) params.append('assigned_to', filters.assigned_to.toString());
    if (filters?.search) params.append('search', filters.search);
    if (filters?.per_page) params.append('per_page', filters.per_page.toString());
    if (filters?.page) params.append('page', filters.page.toString());
    
    const queryString = params.toString();
    const url = queryString ? `/customers?${queryString}` : '/customers';
    
    const response = await api.get<PaginatedCustomers>(url);
    return response.data;
  },

  /**
   * Busca cliente por ID.
   */
  async getById(id: number): Promise<Customer> {
    const response = await api.get<{ data: Customer }>(`/customers/${id}`);
    return response.data.data;
  },

  /**
   * Cria novo cliente.
   */
  async create(data: CustomerFormData): Promise<Customer> {
    const response = await api.post<{ data: Customer }>('/customers', data);
    return response.data.data;
  },

  /**
   * Atualiza cliente existente.
   */
  async update(id: number, data: CustomerFormData): Promise<Customer> {
    const response = await api.put<{ data: Customer }>(`/customers/${id}`, data);
    return response.data.data;
  },

  /**
   * Remove cliente.
   */
  async delete(id: number): Promise<void> {
    await api.delete(`/customers/${id}`);
  },
};

export default customerService;
