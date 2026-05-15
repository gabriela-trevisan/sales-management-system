/**
 * Formata documento (CPF ou CNPJ) para exibição.
 * 
 * @param document - Documento sem formatação (apenas números)
 * @returns CPF: 123.456.789-00 | CNPJ: 12.345.678/0001-90
 */
export const formatDocument = (document: string): string => {
  if (!document) return '';
  
  const numbers = document.replace(/\D/g, '');
  
  if (numbers.length === 11) {
    return numbers.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
  }
  
  if (numbers.length === 14) {
    return numbers.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
  }
  
  return numbers;
};

/**
 * Remove formatação de documento.
 * 
 * @param document - Documento formatado
 * @returns Apenas números
 */
export const cleanDocument = (document: string): string => {
  return document.replace(/\D/g, '');
};

/**
 * Formata telefone para exibição.
 * 
 * @param phone - Telefone sem formatação
 * @returns Fixo: (11) 3456-7890 | Celular: (11) 98765-4321
 */
export const formatPhone = (phone: string): string => {
  if (!phone) return '';
  
  const numbers = phone.replace(/\D/g, '');
  
  if (numbers.length === 11) {
    return numbers.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
  }
  
  if (numbers.length === 10) {
    return numbers.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
  }
  
  return numbers;
};

/**
 * Formata valor monetário (pt-BR).
 * 
 * @param value - Valor numérico
 * @returns Valor formatado: R$ 1.234,56
 */
export const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value);
};

/**
 * Formata data para exibição (pt-BR).
 * 
 * @param date - Data em string ISO ou objeto Date
 * @returns Data formatada: 17/01/2026
 */
export const formatDate = (date: string | Date): string => {
  return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
};

/**
 * Formata data e hora para exibição (pt-BR).
 * 
 * @param date - Data em string ISO ou objeto Date
 * @returns Data e hora formatadas: 17/01/2026 21:30
 */
export const formatDateTime = (date: string | Date): string => {
  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short',
  }).format(new Date(date));
};
