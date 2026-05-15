import api from '../../../services/api';

/**
 * Proposal data structure
 */
export interface Proposal {
  id: number;
  number: string;
  customer_id: number;
  customer: {
    id: number;
    name: string;
    document: string;
    email: string;
  };
  opportunity_id: number | null;
  issue_date: string;
  expiration_date: string;
  notes: string | null;
  status: 'draft' | 'sent' | 'approved' | 'rejected' | 'expired';
  subtotal: number;
  discount: number;
  total: number;
  created_by: number;
  creator: {
    id: number;
    name: string;
    email: string;
  };
  items: ProposalItem[];
  is_expired: boolean;
  can_be_edited: boolean;
  created_at: string;
  updated_at: string;
  deleted_at: string | null;
}

/**
 * Proposal item data structure
 */
export interface ProposalItem {
  id?: number;
  proposal_id?: number;
  product_id: number;
  product?: {
    id: number;
    name: string;
    sku: string;
    category?: {
      id: number;
      name: string;
    };
  };
  description: string | null;
  quantity: number;
  unit_price: number;
  discount_percentage: number;
  total: number;
}

/**
 * Proposal list filters
 */
export interface ProposalFilters {
  status?: string;
  customer_id?: number;
  search?: string;
  per_page?: number;
  page?: number;
}

/**
 * Proposal creation data
 */
export interface CreateProposalData {
  customer_id: number;
  opportunity_id?: number | null;
  issue_date: string;
  expiration_date: string;
  notes?: string | null;
  status: 'draft' | 'sent' | 'approved' | 'rejected' | 'expired';
  items: Array<{
    product_id: number;
    description?: string | null;
    quantity: number;
    unit_price: number;
    discount_percentage?: number;
  }>;
}

/**
 * Proposal update data
 */
export type UpdateProposalData = Partial<CreateProposalData>;

/**
 * Proposal service for API interactions
 */
const proposalService = {
  /**
   * Get all proposals with optional filters and pagination
   */
  async getAll(filters: ProposalFilters = {}) {
    const response = await api.get('/proposals', { params: filters });
    return response.data;
  },

  /**
   * Get a single proposal by ID
   */
  async getById(id: number) {
    const response = await api.get(`/proposals/${id}`);
    // Laravel Resource retorna {data: {...}} para single resource com ->response()
    return response.data.data;
  },

  /**
   * Create a new proposal
   */
  async create(data: CreateProposalData) {
    const response = await api.post('/proposals', data);
    return response.data.data;
  },

  /**
   * Update an existing proposal
   */
  async update(id: number, data: UpdateProposalData) {
    const response = await api.put(`/proposals/${id}`, data);
    return response.data.data;
  },

  /**
   * Delete a proposal
   */
  async delete(id: number) {
    const response = await api.delete(`/proposals/${id}`);
    return response.data;
  },

  /**
   * Download proposal PDF
   */
  async downloadPdf(id: number) {
    const response = await api.get(`/proposals/${id}/pdf`, {
      responseType: 'blob'
    });
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `proposta-${id}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  },

  /**
   * Send proposal by email
   */
  async sendEmail(id: number, email?: string) {
    const response = await api.post(`/proposals/${id}/send-email`, { email });
    return response.data;
  },
};

export default proposalService;
