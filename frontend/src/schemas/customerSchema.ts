import { z } from 'zod';
import { validateDocument, validatePhone, validateEmail } from '../utils/validators';

/**
 * Schema de validação para Customer usando Zod.
 * 
 * Espelha as validações do backend Laravel com mensagens em português.
 * Utiliza algoritmos oficiais para validação de CPF/CNPJ.
 */

export const customerSchema = z.object({
  name: z
    .string()
    .min(1, 'O nome é obrigatório')
    .min(3, 'O nome deve ter no mínimo 3 caracteres')
    .max(255, 'O nome deve ter no máximo 255 caracteres'),

  document: z
    .string()
    .min(1, 'O documento é obrigatório')
    .refine(
      (doc) => {
        const cleanDoc = doc.replace(/\D/g, '');
        return cleanDoc.length === 11 || cleanDoc.length === 14;
      },
      { message: 'O documento deve ser um CPF (11 dígitos) ou CNPJ (14 dígitos)' }
    )
    .refine(
      (doc) => validateDocument(doc),
      { message: 'Documento inválido. Verifique os dígitos verificadores.' }
    ),

  email: z
    .string()
    .min(1, 'O email é obrigatório')
    .email('Email inválido')
    .max(255, 'O email deve ter no máximo 255 caracteres')
    .refine(
      (email) => validateEmail(email),
      { message: 'Email inválido' }
    ),

  phone: z
    .string()
    .nullable()
    .optional()
    .refine(
      (phone) => {
        if (!phone) return true;
        return validatePhone(phone);
      },
      { message: 'Telefone inválido. Use formato (XX) XXXXX-XXXX ou (XX) XXXX-XXXX' }
    ),

  status: z
    .enum(['active', 'inactive', 'prospect', 'churned'], {
      message: 'Status inválido',
    })
    .default('prospect'),

  segment_id: z
    .union([
      z.number().positive('Segmento inválido'),
      z.literal('').transform(() => undefined),
      z.undefined(),
      z.null(),
    ])
    .optional(),
});

/**
 * Schema para criação de cliente.
 * 
 * Validação completa incluindo dígitos verificadores de CPF/CNPJ.
 */
export const createCustomerSchema = customerSchema;

/**
 * Schema para atualização de cliente.
 * 
 * Todos os campos são opcionais para permitir atualização parcial.
 */
export const updateCustomerSchema = customerSchema.partial();

/**
 * Tipo inferido automaticamente do schema Zod.
 * 
 * Fornece type-safety completo para formulários de cliente.
 */
export type CustomerFormData = z.infer<typeof customerSchema>;

/**
 * Mensagens de erro reutilizáveis.
 */
export const errorMessages = {
  required: 'Este campo é obrigatório',
  invalidEmail: 'Email inválido',
  invalidDocument: 'Documento inválido',
  invalidPhone: 'Telefone inválido',
  minLength: (min: number) => `Deve ter no mínimo ${min} caracteres`,
  maxLength: (max: number) => `Deve ter no máximo ${max} caracteres`,
};
