# Plano de Portfólio - Projetos GitHub

**Data:** 7 de Janeiro de 2026  
**Objetivo:** Projeto pessoal no GitHub para incrementar portfólio demonstrando conhecimento em sistemas coorporativos de vendas.

## 💻 Stack Tecnológica

### Primária
- PHP / Laravel
- JavaScript
- MySQL
- PHPUnit
- Docker

### Secundária
- React
- Next.js
- TypeScript

---

## 🚀 Distribuição dos Projetos

### Projeto 1: Sales Management System
**Área:** Vendas / CRM  
**Nicho:** Consultoria e Desenvolvimento de Software Customizado

**Stack Tecnológica:**
- **Backend:** PHP 8.3 / Laravel 11 (API RESTful + DDD + CQRS)
- **Frontend:** React 19 + TypeScript 5.9 + Vite 7 + shadcn/ui
- **Design System:** Material Design theme (OKLCH color space)
- **Banco de Dados:** MySQL 9.0 (11 índices de performance)
- **Cache:** Redis 7.2
- **Autenticação:** Laravel Sanctum 4 (Opaque Tokens — SPA Cookie + Bearer)
- **Testes:** PHPUnit 11
- **Análise Estática:** PHPStan Level 6
- **Infraestrutura:** Docker Compose + Nginx
- **Documentação:** Swagger/OpenAPI (l5-swagger)

**Features Principais:**
- ✅ Pipeline de vendas com 6 estágios definidos (Prospecção → Discovery → Proposta Técnica → Negociação → Contrato → Ganho) — estágios como referência de dados e visíveis no Dashboard; UI Kanban gerenciável listada separadamente abaixo
- ✅ Dashboard com métricas reais e gráficos (Recharts)
- ✅ CRUD Completo de Clientes com filtros, paginação, validação e segmentação por setor
- ✅ Gestão de clientes segmentados por setor (Indústria, Financeiro, Varejo, Saúde, Logística, Educação)
- ✅ Catálogo de serviços (Horas técnicas: Arquiteto, Dev Sênior/Pleno/Júnior, QA, DevOps, UX/UI)
- ✅ Pacotes de projetos (Discovery, MVP, Squad Dedicado)
- ✅ **CRUD Completo de Produtos** (Categorias, preços, unidades, soft deletes, validação dupla)
- ✅ **Dashboard com filtro mensal e tendências** (Implementado)
  - ✅ Seletor de período mensal (MonthlyPeriodSelector)
  - ✅ Micro-indicadores de tendência nos cards (vs. mês anterior)
  - ✅ Gráfico Pizza/Donut — distribuição de clientes por segmento
  - ✅ Line Chart e Bar Chart (propostas e métricas mensais)
  - ✅ Métricas focadas: Clientes, Produtos e Propostas
  - ⏳ Widgets avançados: Top Performers, Metas e Progresso, Alertas Inteligentes (Pendente)
  - ⏳ **Exportação de Dashboard (PDF, Excel) — ALTA PRIORIDADE** (Pendente)
  - ⏳ Drag-and-drop de widgets (baixa prioridade) (Pendente)
  - Ver detalhes em: `docs/modules/MODULE_2_Dashboard.md` e `docs/quality/DASHBOARD_IMPROVEMENTS.md`
- ✅ **CRUD Completo de Propostas** (criação, edição, exclusão, geração de PDF profissional, envio por email com PDF anexo)
- ⏳ Kanban board com drag-and-drop para pipeline visual
- ⏳ Cálculo de comissões por tipo de serviço
- ⏳ RFM Score e segmentação inteligente
- ⏳ Automação de follow-ups

**Futuras Melhorias UI/UX:**
- 🔄 Substituir selects nativos por Radix UI Select para total controle visual
  - Limitação atual: Selects nativos têm estilização limitada (dropdown controlado pelo SO)
  - Melhoria proposta: Implementar SelectTrigger, SelectContent, SelectItem do Radix UI
  - Benefícios: Design consistente cross-browser, animações customizáveis, melhor acessibilidade

**Diferenciais:**
- Interface moderna com Tailwind CSS 4.1 + shadcn/ui
- Design System Material Design com OKLCH color space
- Componentes acessíveis (Radix UI - WCAG 2.1 AA)
- Tema light/dark adaptativo com transições suaves
- **Arquitetura DDD tática completa**: Entities, Value Objects (`Document`, `Email`, `Phone`, `Money`, `ProposalLineAmount`), Domain Events (`ProposalStatusChanged`), Aggregate Root com `RecordsDomainEvents`
- **CQRS (Command/Handler)**: Application layer com Commands e Handlers desacoplados (`CreateProposalCommand`, `CreateProposalHandler`, etc.)
- **Domain Events + Listeners**: `ProposalStatusChanged` dispara `LogProposalStatusChanged` e `NotifyProposalStatusChanged`
- **Authorization Policies**: `CustomerPolicy` e `ProposalPolicy` via Gates do Laravel
- **RFC 7807**: Error responses padronizadas (Problem Details for HTTP APIs) — `bootstrap/app.php`
- API documentada com Swagger (OpenAPI 3.0 via atributos PHP 8)
- Nicho especializado em consultoria de software (demonstra conhecimento do mercado)
- Produtos realistas: horas técnicas, pacotes de projeto, suporte
- Dashboard com dados reais do banco (não mockado)
- **Segurança de dados**: CPF/CNPJ, telefones e CEPs armazenados sem formatação (apenas números)
- **Validação dupla de CPF/CNPJ**: algoritmo oficial da Receita Federal (dígitos verificadores) no frontend (Zod + `validators.ts`) e no backend (`Document` Value Object)
- **Sanitização automática**: Mutators nos Models para limpeza de dados
- **LGPD Compliance**: Soft deletes + `owen-it/laravel-auditing` (auditoria automática de campos sensíveis)
- **Segurança OWASP**: Rate limiting, Security Headers HTTP (6 headers), token expiration 24h, session fixation prevention, SPA Cookie Auth (httpOnly)

---

## 📝 Boas Práticas

### Documentação
- README detalhado com:
  - Descrição do projeto
  - Tecnologias utilizadas
  - Como rodar (Docker)
  - Screenshots/GIFs
  - Arquitetura do sistema
  - Regras de negócio documentadas

### Código
- Padrões de projeto: Repository (Interface + Eloquent impl.), Command/Handler (CQRS), Domain Events, Value Objects
- SOLID principles
- Clean Code
- PHPDoc e JSDoc/TSDoc completos
- Versionamento semântico

### Dados
- Seeds com dados fictícios realistas
- Migrations bem estruturadas
- Relacionamentos complexos

### Features "Wow"
- Dashboards visuais
- Relatórios exportáveis (PDF, Excel)
- APIs bem documentadas (Swagger/OpenAPI)
- Integrações (ex: APIs de pagamento em sandbox)
- Performance otimizada

### Testes
- ✅ Testes unitários de domínio (Value Objects, Enums, Aggregate totals)
- ⏳ Testes de feature/integração para endpoints HTTP (Pendente)
- ⏳ Coverage report (Pendente)
- ⏳ CI/CD básico (GitHub Actions) (Pendente)
