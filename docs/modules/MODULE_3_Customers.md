# Module 3: CRUD de Clientes

**Status:** ✅ 100% Completo

---

## 📋 Visão Geral

Sistema completo de gerenciamento de clientes com validação profissional, máscaras dinâmicas e compliance LGPD.

---

## 🏗️ Arquitetura Backend (20 arquivos)

### Domain Layer
- `Customer.php` - Model principal com SoftDeletes, Auditable
- `CustomerAddress.php` - Endereços do cliente
- `CustomerContact.php` - Contatos do cliente
- `CustomerSegment.php` - Segmentos de mercado
- `CustomerRepositoryInterface.php` - Interface do repository

### Infrastructure
- `EloquentCustomerRepository.php` - Implementação com eager loading

### Application (CQRS)
- `CreateCustomerCommand.php` - DTO imutável
- `CreateCustomerHandler.php` - Use case de criação

### Presentation
- `CustomerController.php` - 5 endpoints RESTful
- `CustomerSegmentController.php` - Lista segmentos
- `CreateCustomerRequest.php` - Validação de criação
- `UpdateCustomerRequest.php` - Validação de update
- `CustomerResource.php` - JSON Resource

---

## 🎨 Frontend (7 arquivos)

### Services
- `customerService.ts` - Cliente API TypeScript
- `segmentService.ts` - Cliente para segmentos

### Schemas
- `customerSchema.ts` - Validação Zod com algoritmo CPF/CNPJ

### Utils
- `validators.ts` - `validateCPF()`, `validateCNPJ()`, `validateDocument()`
- `formatters.ts` - `formatDocument()`, `formatPhone()`, `formatCurrency()`

### Components
- `CustomerFormModal.tsx` - Modal de formulário com máscaras
- `CustomerListPage.tsx` - Lista com filtros e paginação

---

## 🔒 Segurança e Validação

### Mutators Implementados (Backend)
```php
// Customer.php — delega validação para Value Objects
public function setDocumentAttribute(string $value): void
{
    // Document::fromString valida CPF/CNPJ com dígitos verificadores
    $this->attributes['document'] = Document::fromString($value)->value();
}

public function setEmailAttribute(string $value): void
{
    $this->attributes['email'] = Email::fromString($value)->value();
}

public function setPhoneAttribute(?string $value): void
{
    $phone = Phone::fromString($value);
    $this->attributes['phone'] = $phone?->value();
}
```

**Armazenamento:**
- CPF/CNPJ: apenas números validados (11 ou 14 dígitos) via `Document` Value Object
- Telefone: apenas números (10 ou 11 dígitos) via `Phone` Value Object
- CEP: apenas números (8 dígitos)
- Email: lowercase automático via `Email` Value Object

### Validação Frontend (React)

**Stack Profissional:**
- ✅ `react-hook-form 7.71` - Performance otimizada
- ✅ `zod 4.3` - Type-safe schemas
- ✅ `react-imask 7.6` - Máscaras dinâmicas
- ✅ `@hookform/resolvers 5.2` - Integração Zod

**Máscaras Dinâmicas:**
```typescript
// CPF: 000.000.000-00
// CNPJ: 00.000.000/0000-00
// Muda automaticamente ao digitar!

// Telefone: (00) 0000-0000 ou (00) 00000-0000
// Adapta para fixo ou celular
```

**Validação Algorítmica:**
```typescript
export function validateCPF(cpf: string): boolean {
  const numbers = cpf.replace(/\D/g, '');
  if (numbers.length !== 11) return false;
  
  // Algoritmo de dígitos verificadores
  // ... implementação completa
  
  return digit1 === numbers[9] && digit2 === numbers[10];
}
```

---

## 📡 Endpoints

```bash
GET    /api/customers?search=&status=&page=1&per_page=15
POST   /api/customers
GET    /api/customers/{id}
PUT    /api/customers/{id}
DELETE /api/customers/{id}
GET    /api/customer-segments
```

---

## ✨ Features

### Backend
- ✅ CRUD completo
- ✅ Soft deletes (LGPD)
- ✅ Auditable trait (rastreabilidade)
- ✅ Eager loading (segment, assignedUser)
- ✅ Filtros: search, status, assigned_to
- ✅ Paginação server-side
- ✅ Auto-atribuição de responsável
- ✅ Campo `assigned_to` protegido (não pode ser alterado)
- ✅ Documentação Swagger completa

### Frontend
- ✅ Lista com filtros e busca
- ✅ Paginação funcional
- ✅ Modal de formulário
- ✅ Máscaras adaptativas (CPF/CNPJ, telefone)
- ✅ Validação em tempo real
- ✅ Status badges coloridos
- ✅ ConfirmDialog para exclusão
- ✅ Loading/empty states
- ✅ Formatação pt-BR

---

## 🎯 Regras de Negócio

1. **Responsável Automático:**
   - Campo `assigned_to` = usuário que criou
   - Não pode ser alterado após criação
   - Protegido no UpdateCustomerRequest

2. **Documento Único:**
   - CPF/CNPJ deve ser único no sistema
   - Validação com exclusão do registro atual em updates

3. **Email Único:**
   - Email deve ser único
   - Converted para lowercase

4. **Status:**
   - `active` - Cliente ativo
   - `inactive` - Cliente inativo
   - `prospect` - Prospecto (ainda não cliente)
   - `churned` - Cliente perdido

---

## 🧪 Validações

```bash
✅ CPF válido aceito: 529.982.247-25
✅ CPF inválido rejeitado: 111.111.111-11
✅ CNPJ válido aceito: 11.222.333/0001-81
✅ Telefone fixo: (11) 3721-4321
✅ Telefone celular: (11) 98765-4321
✅ Máscara adapta automaticamente
✅ Armazenamento apenas números
✅ Busca funciona sem pontuação
✅ Email convertido para lowercase
✅ Soft delete funciona
✅ Audit logs criados (LGPD)
```

---

## 🎨 UI/UX

### CustomerFormModal
- Campos organizados em seções
- Validação inline com ícone AlertCircle
- Controller para inputs mascarados
- Reset automático ao abrir/fechar
- Defaults inteligentes

### CustomerListPage
- Tabela responsiva
- Colunas: Nome, Documento, Email, Segmento, Status, Responsável, Ações
- Filtros: Busca (nome/email/documento), Status, Página
- Badges coloridos para status
- Botões Edit (azul) + Delete (vermelho)
- Confirmação moderna para exclusão

---

_Module 3 estabelece padrão de qualidade para todos os CRUDs do projeto._
