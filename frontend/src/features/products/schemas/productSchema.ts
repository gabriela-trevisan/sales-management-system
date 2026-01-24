import { z } from 'zod';

/**
 * Schema de validação para criação de produto.
 */
export const createProductSchema = z.object({
  name: z.string()
    .min(1, 'Nome é obrigatório')
    .max(255, 'Nome deve ter no máximo 255 caracteres'),
  
  sku: z.string()
    .min(1, 'SKU é obrigatório')
    .max(255, 'SKU deve ter no máximo 255 caracteres')
    .regex(/^[A-Z0-9-_]+$/, 'SKU deve conter apenas letras maiúsculas, números, hífens e underscores'),
  
  description: z.string().optional(),
  
  category_id: z.number().int().positive().optional(),
  
  base_price: z.number().min(0, 'Preço base deve ser maior ou igual a zero'),
  
  cost_price: z.number().min(0, 'Preço de custo deve ser maior ou igual a zero').optional(),
  
  unit: z.enum(['unit', 'kg', 'liter', 'meter', 'hour', 'month']),
  
  is_active: z.boolean(),
  
  requires_approval: z.boolean(),
  
  specifications: z.record(z.string(), z.unknown()).optional(),
});

/**
 * Schema de validação para atualização de produto.
 */
export const updateProductSchema = createProductSchema.partial();

/**
 * Tipo inferido do schema de criação.
 */
export type ProductFormData = z.infer<typeof createProductSchema>;

/**
 * Tipo inferido do schema de atualização.
 */
export type ProductUpdateData = z.infer<typeof updateProductSchema>;

/**
 * Opções de unidades disponíveis.
 */
export const unitOptions = [
  { value: 'unit', label: 'Unidade' },
  { value: 'kg', label: 'Quilograma (kg)' },
  { value: 'liter', label: 'Litro (L)' },
  { value: 'meter', label: 'Metro (m)' },
  { value: 'hour', label: 'Hora (h)' },
  { value: 'month', label: 'Mês' },
] as const;
