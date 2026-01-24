import api from '@/services/api';

/**
 * Interface do modelo Product.
 */
export interface Product {
  id: number;
  name: string;
  sku: string;
  description?: string;
  category?: {
    id: number;
    name: string;
  };
  base_price: number;
  cost_price?: number;
  unit: 'unit' | 'kg' | 'liter' | 'meter' | 'hour' | 'month';
  is_active: boolean;
  requires_approval: boolean;
  specifications?: Record<string, unknown>;
  created_at: string;
  updated_at: string;
}

/**
 * Filtros disponíveis para listagem de produtos.
 */
export interface ProductFilters {
  is_active?: boolean;
  category_id?: number;
  search?: string;
  per_page?: number;
  page?: number;
}

/**
 * Resposta paginada da API de produtos.
 */
export interface PaginatedProducts {
  data: Product[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

/**
 * Dados para criação/atualização de produto.
 */
export interface ProductFormData {
  name: string;
  sku: string;
  description?: string;
  category_id?: number;
  base_price: number;
  cost_price?: number;
  unit: string;
  is_active?: boolean;
  requires_approval?: boolean;
  specifications?: Record<string, unknown>;
}

/**
 * Serviço para operações CRUD de produtos.
 */
const productService = {
  /**
   * Lista produtos com filtros e paginação.
   */
  async getAll(filters?: ProductFilters): Promise<PaginatedProducts> {
    const params = new URLSearchParams();
    
    if (filters?.is_active !== undefined) params.append('is_active', filters.is_active.toString());
    if (filters?.category_id) params.append('category_id', filters.category_id.toString());
    if (filters?.search) params.append('search', filters.search);
    if (filters?.per_page) params.append('per_page', filters.per_page.toString());
    if (filters?.page) params.append('page', filters.page.toString());
    
    const queryString = params.toString();
    const url = queryString ? `/products?${queryString}` : '/products';
    
    const response = await api.get<PaginatedProducts>(url);
    return response.data;
  },

  /**
   * Busca produto por ID.
   */
  async getById(id: number): Promise<Product> {
    const response = await api.get<{ data: Product }>(`/products/${id}`);
    return response.data.data;
  },

  /**
   * Cria novo produto.
   */
  async create(data: ProductFormData): Promise<Product> {
    const response = await api.post<{ data: Product }>('/products', data);
    return response.data.data;
  },

  /**
   * Atualiza produto existente.
   */
  async update(id: number, data: Partial<ProductFormData>): Promise<Product> {
    const response = await api.put<{ data: Product }>(`/products/${id}`, data);
    return response.data.data;
  },

  /**
   * Deleta produto (soft delete).
   */
  async delete(id: number): Promise<void> {
    await api.delete(`/products/${id}`);
  },
};

export default productService;
