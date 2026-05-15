import { z } from 'zod';

/**
 * Proposal item schema for validation
 */
export const proposalItemSchema = z.object({
  product_id: z.number().positive('Produto é obrigatório'),
  description: z.string().max(1000, 'Descrição muito longa').optional().or(z.literal('')),
  quantity: z.number().min(1, 'Quantidade deve ser no mínimo 1'),
  unit_price: z.number().min(0, 'Preço unitário deve ser maior ou igual a zero'),
  discount_percentage: z.number().min(0, 'Desconto não pode ser negativo').max(100, 'Desconto não pode ser maior que 100%').optional(),
});

/**
 * Proposal schema for form validation
 */
export const proposalSchema = z.object({
  customer_id: z.number().positive('Cliente é obrigatório'),
  opportunity_id: z.number().positive().optional().or(z.literal(null)),
  issue_date: z.string().min(1, 'Data de emissão é obrigatória').refine((date) => {
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    return dateRegex.test(date);
  }, 'Data deve estar no formato YYYY-MM-DD'),
  expiration_date: z.string().min(1, 'Data de validade é obrigatória').refine((date) => {
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    return dateRegex.test(date);
  }, 'Data deve estar no formato YYYY-MM-DD'),
  notes: z.string().max(5000, 'Observações muito longas').optional().or(z.literal('')),
  status: z.enum(['draft', 'sent', 'approved', 'rejected', 'expired']),
  items: z.array(proposalItemSchema).min(1, 'É necessário adicionar pelo menos um item'),
}).refine((data) => {
  const issueDate = new Date(data.issue_date);
  const expirationDate = new Date(data.expiration_date);
  return expirationDate > issueDate;
}, {
  message: 'Data de validade deve ser posterior à data de emissão',
  path: ['expiration_date'],
});

export type ProposalFormData = z.infer<typeof proposalSchema>;
export type ProposalItemFormData = z.infer<typeof proposalItemSchema>;
