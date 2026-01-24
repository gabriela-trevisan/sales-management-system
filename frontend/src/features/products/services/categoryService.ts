import api from '@/services/api';

/**
 * Interface do modelo ProductCategory.
 */
export interface ProductCategory {
  id: number;
  name: string;
  description?: string;
}

/**
 * Serviço para operações com categorias de produtos.
 */
const categoryService = {
  /**
   * Lista todas as categorias de produtos.
   */
  async getAll(): Promise<ProductCategory[]> {
    const response = await api.get<{ data: ProductCategory[] }>('/product-categories');
    return response.data.data;
  },
};

export default categoryService;
