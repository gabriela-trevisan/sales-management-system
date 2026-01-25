# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

---

## [Unreleased]

### Module 6 - Propostas Comerciais (70% Completo)

#### Adicionado em 2026-01-24

**Backend:**

- ✅ Models: Proposal, ProposalItem (SoftDeletes, Auditable)
- ✅ Repository Pattern + CQRS (Command/Handler)
- ✅ Controller RESTful com 7 endpoints (CRUD + PDF + Email)
- ✅ Form Requests: Create, Update
- ✅ Resources: Proposal, ProposalItem
- ✅ OpenAPI Schema + Seeder

**Frontend:**

- ✅ proposalService.ts - Cliente API
- ✅ proposalSchema.ts - Validação Zod
- ✅ ProposalFormModal.tsx - Formulário com gestão dinâmica de itens
- ✅ ProposalListPage.tsx - Listagem com filtros

**Features Implementadas:**

- ✅ CRUD completo de propostas comerciais
- ✅ Gestão dinâmica de itens (add/remove linhas)
- ✅ Auto-preenchimento de preço ao selecionar produto
- ✅ Cálculos em tempo real (quantidade × preço × (1 - desconto%))
- ✅ Auto-geração de número sequencial (PROP-YYYY-XXXX)
- ✅ Listagem com filtros (status, customer, search)
- ✅ Validação dupla (Zod frontend + Laravel backend)
- ✅ Soft deletes para LGPD compliance
- ✅ Eager loading (customer, creator, items.product) - previne N+1
- ✅ Status badges coloridos (5 estados: draft, sent, approved, rejected, expired)
- ✅ opportunity_id nullable (propostas independentes)
- ✅ Business rules: isExpired(), canBeEdited()
- ✅ Formatação pt-BR (R$, datas)
- ✅ Modal responsivo com scroll
- ✅ Loading states em todas mutations
- ✅ Alerts de sucesso/erro
- ✅ Repository pattern + CQRS
- ✅ OpenAPI/Swagger documentado
- ✅ React Compiler compatible

**Endpoints Criados:**

```
GET    /api/proposals?status=&customer_id=&search=&per_page=15
POST   /api/proposals
GET    /api/proposals/{id}
PUT    /api/proposals/{id}
DELETE /api/proposals/{id}
```

**Correções e Otimizações:**

- ✅ CreateProposalCommand padronizado: `class` com `public readonly` + método `toArray()`
- ✅ UpdateProposalData: type alias ao invés de interface vazia
- ✅ useWatch: Substituído `watch()` para compatibilidade React Compiler
- ✅ setValue: Auto-fill de preço sem re-render desnecessário
- ✅ useMemo: Cálculos de totais memoizados
- ✅ Migration: opportunity_id sem FK constraint (aguarda Module 4)
- ✅ Seeder: Defensivo com min() para evitar array index errors
- ✅ Build: 11.83s com sucesso (0 erros TypeScript/PHP)

**Pendente (30% Module 6):**

- ❌ ProposalViewPage (visualização detalhada read-only)
- ❌ PDF Generation (geração de proposta em PDF profissional)
- ❌ Email Sending (envio de proposta por email)
- ❌ Proposal Versioning (sistema de versionamento)
- ❌ Opportunity Integration (quando Module 4 implementado)
- ❌ Unit Tests (backend e frontend)

**Documentação:**

- ✅ `frontend/src/features/proposals/components/README.md` - Documentação completa do ProposalFormModal
- ✅ README.md atualizado com features de propostas
- ✅ CHANGELOG.md criado

---

## [0.5.0] - 2026-01-24 - Module 5: Products CRUD

### Adicionado

- CRUD completo de produtos/serviços
- Listagem com filtros (busca, status, categoria)
- ProductFormModal com validação Zod
- 8 categorias de produtos
- 6 tipos de unidade (unit, kg, liter, meter, hour, month)
- Campo SKU validado com regex
- Campo specifications JSON customizável
- Checkboxes is_active e requires_approval
- OpenAPI/Swagger documentação completa

---

## [0.4.0] - 2026-01-18 - Fase 4: Correções TypeScript + Docker

### Corrigido

- TypeScript: verbatimModuleSyntax - type-only imports
- Zod: Schemas simplificados (removido .nullable().optional().transform())
- Docker Compose: Removido `version` (v2+ compatible)
- API Interceptor: Loop infinito no refresh token
- Customer Model: Relationship opportunities() comentado (aguarda Module 4)

---

## [0.3.0] - 2026-01-17 - Module 3: Customers CRUD

### Adicionado

- CRUD completo de clientes
- CustomerFormModal com validação profissional
- react-hook-form + Zod + react-imask
- Máscaras dinâmicas (CPF↔CNPJ, telefone)
- Validação de CPF/CNPJ com dígitos verificadores
- Auto-atribuição de responsável
- 6 segmentos de cliente (Indústria, Financeiro, Varejo, Saúde, Logística, Educação)
- Seeders com CNPJs válidos matematicamente

---

## [0.2.0] - 2026-01-15 - Module 2: Dashboard

### Adicionado

- Dashboard com métricas reais
- 4 cards: Clientes, Oportunidades, Pipeline Value, Conversion Rate
- LineChart vendas mensais (Recharts)
- BarChart oportunidades por estágio
- Timeline de atividades recentes
- Formatação pt-BR (R$, datas)

---

## [0.1.0] - 2026-01-10 - Module 1: Auth + Layout

### Adicionado

- Autenticação JWT com Laravel Sanctum
- LoginPage split screen moderno
- Layout com Sidebar + Header
- AuthContext e proteção de rotas
- Interceptor Axios para JWT
- Logout funcional

---

## [0.0.1] - 2026-01-05 - Module 0: Infraestrutura

### Adicionado

- Docker Compose com 6 containers
- Laravel 11.47 com DDD
- React 19 + TypeScript + Vite
- Tailwind CSS 4.1
- 21 migrations
- 8 seeders
- MySQL 9.0 + Redis 7.2

---

[unreleased]: https://github.com/gabriela-trevisan/sales-management-system/compare/v0.5.0...HEAD
[0.5.0]: https://github.com/gabriela-trevisan/sales-management-system/releases/tag/v0.5.0
[0.4.0]: https://github.com/gabriela-trevisan/sales-management-system/releases/tag/v0.4.0
[0.3.0]: https://github.com/gabriela-trevisan/sales-management-system/releases/tag/v0.3.0
[0.2.0]: https://github.com/gabriela-trevisan/sales-management-system/releases/tag/v0.2.0
[0.1.0]: https://github.com/gabriela-trevisan/sales-management-system/releases/tag/v0.1.0
[0.0.1]: https://github.com/gabriela-trevisan/sales-management-system/releases/tag/v0.0.1
