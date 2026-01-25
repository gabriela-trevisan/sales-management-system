# Code Quality - Padrões e Documentação

---

## 📚 Documentação Padronizada

### Backend - PHPDoc

**Padrão:** PHPDoc oficial PHP com tags completas

**Exemplo:**
```php
/**
 * Create a new customer in the system.
 *
 * @param CreateCustomerRequest $request
 * @return \Illuminate\Http\JsonResponse
 * @throws \Illuminate\Validation\ValidationException
 */
public function store(CreateCustomerRequest $request): JsonResponse
{
    // ...
}
```

**Tags Utilizadas:**
- `@param` - Parâmetros com tipo
- `@return` - Retorno com tipo
- `@throws` - Exceções possíveis
- `@var` - Variáveis
- `@property` - Propriedades do model

**Arquivos Revisados:**
- CreateCustomerRequest.php
- UpdateCustomerRequest.php
- CustomerController.php
- CreateCustomerCommand.php
- Customer.php, CustomerAddress.php
- AuthController.php
- api.php (rotas documentadas)

---

### Frontend - JSDoc/TSDoc

**Padrão:** JSDoc para TypeScript com tipos inferidos

**Exemplo:**
```typescript
/**
 * Valida um CPF brasileiro.
 *
 * @param cpf - CPF com ou sem formatação
 * @returns true se válido, false caso contrário
 *
 * @example
 * validateCPF('529.982.247-25') // true
 * validateCPF('111.111.111-11') // false
 */
export function validateCPF(cpf: string): boolean {
  // ...
}
```

**Tags Utilizadas:**
- `@param` - Parâmetros
- `@returns` - Retorno
- `@example` - Exemplos de uso
- `@throws` - Exceções

**Arquivos Revisados:**
- validators.ts - Validação com exemplos
- formatters.ts - Formatação com outputs esperados
- customerSchema.ts - Documentação Zod
- customerService.ts - CRUD completo
- authService.ts, dashboardService.ts
- segmentService.ts

---

## 🎨 Componentes UI Reutilizáveis

### ConfirmDialog

**Propósito:** Modal moderno de confirmação (substituiu window.confirm)

**Features:**
- ✅ 3 variantes: danger, warning, info
- ✅ Backdrop com blur effect
- ✅ Animações suaves (fade + zoom)
- ✅ Ícone temático (AlertTriangle)
- ✅ Botão X para fechar
- ✅ Fecha ao clicar fora
- ✅ Labels customizáveis
- ✅ Totalmente documentado (JSDoc)

**Uso:**
```typescript
<ConfirmDialog
  isOpen={deleteConfirm.isOpen}
  title="Excluir cliente"
  message="Tem certeza que deseja excluir este cliente?"
  confirmLabel="Excluir"
  cancelLabel="Cancelar"
  onConfirm={confirmDelete}
  onCancel={handleCancel}
  variant="danger"
/>
```

**Benefícios:**
- ✨ UX profissional
- ♿ Acessível e responsivo
- 🔄 Reutilizável
- 🎨 Consistência visual

---

## 🏗️ Arquitetura de Código

### Backend - DDD + CQRS

**Estrutura:**
```
app/
├── Domain/              # Modelos de domínio + contratos
│   └── Customer/
│       ├── Models/
│       │   └── Customer.php
│       └── Contracts/
│           └── CustomerRepositoryInterface.php
│
├── Application/         # Use Cases (CQRS)
│   └── Customer/
│       └── CreateCustomer/
│           ├── CreateCustomerCommand.php (DTO)
│           └── CreateCustomerHandler.php (Use Case)
│
├── Infrastructure/      # Implementações técnicas
│   └── Repositories/
│       └── EloquentCustomerRepository.php
│
└── Presentation/        # Controllers + Resources
    └── Http/
        ├── Controllers/
        ├── Requests/
        └── Resources/
```

**Padrões:**
- ✅ Repository Pattern
- ✅ CQRS (Command Query Responsibility Segregation)
- ✅ DTO (Data Transfer Objects)
- ✅ Service Provider para bindings
- ✅ Form Requests para validação
- ✅ API Resources para transformação

---

### Frontend - Feature Modules

**Estrutura:**
```
src/features/
└── customers/
    ├── components/
    │   ├── CustomerFormModal.tsx
    │   └── CustomerListPage.tsx
    ├── services/
    │   └── customerService.ts
    ├── schemas/
    │   └── customerSchema.ts
    └── hooks/           # Futuro
        └── useCustomers.ts
```

**Padrões:**
- ✅ Feature-first organization
- ✅ Colocation (arquivos relacionados juntos)
- ✅ Services para API calls
- ✅ Schemas para validação
- ✅ Hooks customizados (futuro)

---

## ✅ Convenções de Código

### Naming

**Backend (PHP):**
- Classes: PascalCase (CustomerController)
- Methods: camelCase (getAll)
- Variables: camelCase ($customerId)
- Constants: UPPER_CASE (MAX_ITEMS)
- Database: snake_case (customer_id)

**Frontend (TypeScript):**
- Components: PascalCase (CustomerFormModal)
- Functions: camelCase (validateCPF)
- Variables: camelCase (customerId)
- Constants: UPPER_CASE (API_BASE_URL)
- Types/Interfaces: PascalCase (Customer)

### Files

**Backend:**
- Controllers: `{Entity}Controller.php`
- Models: `{Entity}.php`
- Requests: `Create{Entity}Request.php`
- Resources: `{Entity}Resource.php`
- Commands: `{Action}{Entity}Command.php`

**Frontend:**
- Components: `{Name}.tsx`
- Services: `{entity}Service.ts`
- Schemas: `{entity}Schema.ts`
- Utils: `{purpose}.ts`

---

## 🧪 Code Quality Tools

### Backend

**PHPStan Level 6:**
```bash
docker exec sms_backend vendor/bin/phpstan analyse --memory-limit=512M
```
- ✅ 22 pontos de melhoria detectados
- ✅ Type hints completos
- ✅ Detecção de bugs em potencial

**PHP CS Fixer (futuro):**
```bash
docker exec sms_backend vendor/bin/php-cs-fixer fix
```

---

### Frontend

**TypeScript Strict Mode:**
```json
{
  "compilerOptions": {
    "strict": true,
    "verbatimModuleSyntax": true
  }
}
```
- ✅ Type-only imports
- ✅ Strict null checks
- ✅ No implicit any

**ESLint (configurado):**
```bash
docker exec sms_frontend npm run lint
```

---

## 📝 Comentários

### Quando Comentar

**✅ BOM:**
```php
// Calcula dígitos verificadores do CPF segundo algoritmo da Receita Federal
$digit1 = $this->calculateDigit($numbers, 10);
```

**❌ RUIM:**
```php
// Incrementa o contador
$counter++;
```

### Regras
- ✅ Documente WHY, não WHAT
- ✅ Explique regras de negócio complexas
- ✅ PHPDoc/JSDoc em funções públicas
- ❌ Evite comentários redundantes
- ❌ Não deixe código comentado
- ❌ Não use TODOs sem contexto

---

## 🎯 Code Review Checklist

### Backend
- [ ] PHPDoc completo
- [ ] Type hints em parâmetros e retornos
- [ ] Validação de entrada (Form Requests)
- [ ] Repository pattern usado
- [ ] Eager loading quando necessário
- [ ] Cache invalidado corretamente
- [ ] Soft deletes quando aplicável
- [ ] Audit logs para LGPD

### Frontend
- [ ] JSDoc em funções exportadas
- [ ] Type-only imports
- [ ] Validação Zod
- [ ] Loading/error states
- [ ] Cleanup em useEffect
- [ ] Acessibilidade (a11y)
- [ ] Responsivo
- [ ] Formatação pt-BR

---

## 📊 Métricas de Qualidade

### Backend
- ✅ PHPStan Level 6 passing
- ✅ 0 PHP errors
- ✅ Swagger 100% atualizado
- ✅ Repository pattern em 100% dos CRUDs

### Frontend
- ✅ 0 TypeScript errors
- ✅ Build time: ~8.5s
- ✅ Zod schemas em 100% dos forms
- ✅ Máscaras em campos de entrada

---

## 🚀 Melhorias Contínuas

### Implementado
- ✅ PHPDoc/JSDoc padronizado
- ✅ Componentes reutilizáveis
- ✅ Arquitetura DDD + CQRS
- ✅ Type safety completo

### Planejado
- ⏳ Testes unitários (PHPUnit, Vitest)
- ⏳ Testes E2E (Cypress)
- ⏳ Code coverage (80%+)
- ⏳ Automated refactoring tools
- ⏳ Performance profiling

---

_Qualidade de código é fundamental para manutenibilidade a longo prazo._
