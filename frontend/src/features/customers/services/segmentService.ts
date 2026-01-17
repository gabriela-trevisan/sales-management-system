import api from '@/services/api';

/**
 * Segmento de cliente.
 */
export interface Segment {
  id: number;
  name: string;
  description?: string;
}

/**
 * Serviço para segmentos de clientes.
 */
const segmentService = {
  /**
   * Lista todos os segmentos disponíveis.
   */
  async getAll(): Promise<Segment[]> {
    const response = await api.get<{ data: Segment[] }>('/customer-segments');
    return response.data.data;
  },
};

export default segmentService;
