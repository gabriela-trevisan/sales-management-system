/**
 * Validadores de documentos e dados brasileiros.
 * 
 * Implementa algoritmos oficiais da Receita Federal para CPF e CNPJ.
 */

/**
 * Valida CPF (Cadastro de Pessoas Físicas).
 * 
 * Utiliza algoritmo oficial com verificação de dígitos.
 * 
 * @param cpf - CPF com ou sem formatação
 * @returns true se válido, false caso contrário
 * 
 * @example
 * validateCPF('123.456.789-09') // true ou false
 * validateCPF('12345678909') // true ou false
 */
export function validateCPF(cpf: string): boolean {
  // Remove caracteres não numéricos
  const cleanCPF = cpf.replace(/\D/g, '');

  // Verifica se tem 11 dígitos
  if (cleanCPF.length !== 11) {
    return false;
  }

  // Verifica se todos os dígitos são iguais (CPF inválido)
  if (/^(\d)\1{10}$/.test(cleanCPF)) {
    return false;
  }

  // Valida primeiro dígito verificador
  let sum = 0;
  for (let i = 0; i < 9; i++) {
    sum += parseInt(cleanCPF.charAt(i)) * (10 - i);
  }
  let digit1 = 11 - (sum % 11);
  if (digit1 >= 10) digit1 = 0;

  if (digit1 !== parseInt(cleanCPF.charAt(9))) {
    return false;
  }

  // Valida segundo dígito verificador
  sum = 0;
  for (let i = 0; i < 10; i++) {
    sum += parseInt(cleanCPF.charAt(i)) * (11 - i);
  }
  let digit2 = 11 - (sum % 11);
  if (digit2 >= 10) digit2 = 0;

  return digit2 === parseInt(cleanCPF.charAt(10));
}

/**
 * Valida CNPJ (Cadastro Nacional de Pessoa Jurídica).
 * 
 * Utiliza algoritmo oficial com verificação de dígitos.
 * 
 * @param cnpj - CNPJ com ou sem formatação
 * @returns true se válido, false caso contrário
 * 
 * @example
 * validateCNPJ('12.345.678/0001-90') // true ou false
 * validateCNPJ('12345678000190') // true ou false
 */
export function validateCNPJ(cnpj: string): boolean {
  // Remove caracteres não numéricos
  const cleanCNPJ = cnpj.replace(/\D/g, '');

  // Verifica se tem 14 dígitos
  if (cleanCNPJ.length !== 14) {
    return false;
  }

  // Verifica se todos os dígitos são iguais (CNPJ inválido)
  if (/^(\d)\1{13}$/.test(cleanCNPJ)) {
    return false;
  }

  // Valida primeiro dígito verificador
  let length = cleanCNPJ.length - 2;
  let numbers = cleanCNPJ.substring(0, length);
  const digits = cleanCNPJ.substring(length);
  let sum = 0;
  let pos = length - 7;

  for (let i = length; i >= 1; i--) {
    sum += parseInt(numbers.charAt(length - i)) * pos--;
    if (pos < 2) pos = 9;
  }

  let result = sum % 11 < 2 ? 0 : 11 - (sum % 11);
  if (result !== parseInt(digits.charAt(0))) {
    return false;
  }

  // Valida segundo dígito verificador
  length = length + 1;
  numbers = cleanCNPJ.substring(0, length);
  sum = 0;
  pos = length - 7;

  for (let i = length; i >= 1; i--) {
    sum += parseInt(numbers.charAt(length - i)) * pos--;
    if (pos < 2) pos = 9;
  }

  result = sum % 11 < 2 ? 0 : 11 - (sum % 11);
  return result === parseInt(digits.charAt(1));
}

/**
 * Valida documento (CPF ou CNPJ) automaticamente.
 * 
 * Detecta o tipo baseado no número de dígitos.
 * 
 * @param document - Documento com ou sem formatação
 * @returns true se válido, false caso contrário
 * 
 * @example
 * validateDocument('123.456.789-09') // valida como CPF
 * validateDocument('12.345.678/0001-90') // valida como CNPJ
 */
export function validateDocument(document: string): boolean {
  const cleanDoc = document.replace(/\D/g, '');

  if (cleanDoc.length === 11) {
    return validateCPF(cleanDoc);
  }

  if (cleanDoc.length === 14) {
    return validateCNPJ(cleanDoc);
  }

  return false;
}

/**
 * Valida telefone brasileiro.
 * 
 * Aceita telefones fixos (10 dígitos) e celulares (11 dígitos).
 * 
 * @param phone - Telefone com ou sem formatação
 * @returns true se válido, false caso contrário
 * 
 * @example
 * validatePhone('(11) 98765-4321') // true - celular
 * validatePhone('(11) 3456-7890') // true - fixo
 */
export function validatePhone(phone: string): boolean {
  const cleanPhone = phone.replace(/\D/g, '');

  // Telefone deve ter 10 dígitos (fixo) ou 11 dígitos (celular)
  if (cleanPhone.length !== 10 && cleanPhone.length !== 11) {
    return false;
  }

  // Celular deve começar com 9
  if (cleanPhone.length === 11 && cleanPhone.charAt(2) !== '9') {
    return false;
  }

  // DDD deve estar entre 11 e 99
  const ddd = parseInt(cleanPhone.substring(0, 2));
  if (ddd < 11 || ddd > 99) {
    return false;
  }

  return true;
}

/**
 * Valida formato de email.
 * 
 * @param email - Email a ser validado
 * @returns true se válido, false caso contrário
 */
export function validateEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

/**
 * Valida CEP brasileiro.
 * 
 * Verifica se o CEP tem 8 dígitos.
 * 
 * @param cep - CEP com ou sem formatação
 * @returns true se válido, false caso contrário
 */
export function validateCEP(cep: string): boolean {
  const cleanCEP = cep.replace(/\D/g, '');
  return cleanCEP.length === 8;
}
