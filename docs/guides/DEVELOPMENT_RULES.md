# Regras e Objetivos do Projeto

---

## 📝 Regras de Desenvolvimento

### 1. Controle de Versão (Git)

**Commits:**
- ✅ Usuário controla TODOS os commits
- ✅ Mensagens descritivas em português
- ✅ Formato: `tipo: descrição breve`
  - `feat:` Nova funcionalidade
  - `fix:` Correção de bug
  - `docs:` Documentação
  - `refactor:` Refatoração
  - `perf:` Performance
  - `test:` Testes

**Branches:**
- `master` - Branch principal (produção)
- `develop` - Development (futuro)
- `feature/*` - Features específicas (futuro)

**Arquivos Excluídos:**
- ✅ Documentos de planejamento em draft/stash
- ✅ Nunca commitar arquivos temporários
- ✅ `.gitignore` configurado

---

### 2. Estratégia de Desenvolvimento

**Modular:**
- ✅ Completar backend + frontend de cada módulo antes de avançar
- ✅ Testar funcionalidade completa antes de marcar como done
- ✅ Um módulo por vez (foco)

**Validação:**
- ✅ Build frontend sem erros
- ✅ PHPStan passing
- ✅ Swagger atualizado
- ✅ Teste manual das funcionalidades

**Ordem de Implementação:**
1. Module 0: Infraestrutura ✅
2. Module 1: Autenticação ✅
3. Module 2: Dashboard ✅
4. Module 3: Customers ✅
5. Module 5: Products ✅
6. Module 6: Proposals ✅ 100%
7. Module 4: Opportunities ⏳
8. Modules 7-10: Avançados ⏳

---

### 3. Qualidade de Código

**Backend:**
- ✅ PHPDoc em todas as funções públicas
- ✅ Type hints obrigatórios
- ✅ Repository Pattern
- ✅ CQRS para comandos complexos
- ✅ Form Requests para validação
- ✅ API Resources para transformação
- ✅ Soft deletes quando aplicável
- ✅ Auditable trait (LGPD)

**Frontend:**
- ✅ JSDoc em funções exportadas
- ✅ Type-only imports (verbatimModuleSyntax)
- ✅ Zod schemas para validação
- ✅ react-hook-form para formulários
- ✅ Componentes reutilizáveis
- ✅ Loading/error states
- ✅ Formatação pt-BR

---

### 4. Documentação

**Obrigatória:**
- ✅ Swagger para todos os endpoints
- ✅ PHPDoc/JSDoc completo
- ✅ README.md atualizado
- ✅ CHANGELOG.md (Keep a Changelog)
- ✅ Documentação modular em `docs/`

**Opcional mas Recomendado:**
- ⏳ Diagramas de arquitetura
- ⏳ Guias de onboarding
- ⏳ Decisões de arquitetura (ADR)

---

### 5. Testes (Futuro)

**Backend:**
- ⏳ Unit tests (PHPUnit)
- ⏳ Feature tests
- ⏳ Coverage mínimo: 70%

**Frontend:**
- ⏳ Unit tests (Vitest)
- ⏳ E2E tests (Cypress)
- ⏳ Coverage mínimo: 60%

---

## 🎯 Objetivos do Projeto

### Objetivo Principal

**Portfólio Profissional no GitHub** demonstrando:
- ✅ Conhecimento sólido de Laravel (DDD, API REST)
- ✅ React moderno com TypeScript
- ✅ Arquitetura bem estruturada
- ✅ Boas práticas (documentação, segurança)
- ✅ Entendimento de negócio (nicho real)
- ✅ UI/UX moderna e responsiva

---

### Objetivos Técnicos

#### Backend
- ✅ Laravel 11 com arquitetura DDD
- ✅ API RESTful stateless (Bearer tokens)
- ✅ Sanctum para autenticação
- ✅ Repository Pattern + CQRS
- ✅ PHPStan Level 6
- ✅ Swagger/OpenAPI completo
- ✅ Cache Redis estratégico
- ✅ 11 índices de performance
- ✅ LGPD compliance (audit logs)
- ✅ OWASP Top 10 compliance

#### Frontend
- ✅ React 19 + TypeScript 5.9
- ✅ Vite 6.0 para build rápido
- ✅ Tailwind CSS 4.1
- ✅ TanStack Query v5
- ✅ react-hook-form + Zod
- ✅ Máscaras dinâmicas (react-imask)
- ✅ Componentes reutilizáveis
- ✅ Type-safe em 100%
- ✅ Build < 10s

#### DevOps
- ✅ Docker Compose (6 containers)
- ✅ MySQL 9.0 + Redis 7.2
- ✅ Nginx reverse proxy
- ✅ Hot reload (dev)
- ⏳ CI/CD pipeline (futuro)
- ⏳ Deploy automatizado (futuro)

---

### Objetivos de Negócio

**Funcionalidades Mínimas (MVP):**
- ✅ Autenticação segura
- ✅ Dashboard executivo
- ✅ CRUD de Clientes
- ✅ CRUD de Produtos
- 🟡 CRUD de Propostas (70%)
- ⏳ Pipeline Kanban (feature estrela)

**Funcionalidades Avançadas:**
- ⏳ Comissões automatizadas
- ⏳ Analytics e relatórios
- ⏳ Configurações e permissões
- ⏳ Automação de follow-ups

---

### Objetivos de Aprendizado

**Demonstrar Habilidades em:**
- ✅ Arquitetura de Software (DDD, Clean Architecture)
- ✅ Boas Práticas (SOLID, Design Patterns)
- ✅ Segurança (OWASP, LGPD)
- ✅ Performance (Cache, Índices, Eager Loading)
- ✅ Qualidade (PHPStan, TypeScript strict)
- ✅ Documentação (Swagger, PHPDoc, JSDoc)
- ✅ UI/UX (Responsivo, Acessível)
- ⏳ Testes (Unit, Integration, E2E)
- ⏳ DevOps (CI/CD, Deploy)

---

## 🚀 Roadmap

### Fase 1: Foundation ✅
- ✅ Infraestrutura Docker
- ✅ Autenticação e Layout
- ✅ Dashboard com métricas
- ✅ CRUD de Clientes
- ✅ CRUD de Produtos

### Fase 2: Core Features (Atual)
- 🟡 CRUD de Propostas (70%)
- ⏳ Pipeline Kanban
- ⏳ CRUD de Oportunidades
- ⏳ Geração de PDF
- ⏳ Envio de Email

### Fase 3: Advanced
- ⏳ Sistema de Comissões
- ⏳ Analytics e Relatórios
- ⏳ Configurações e Permissões
- ⏳ Automação de Workflows

### Fase 4: Polish
- ⏳ Testes automatizados
- ⏳ CI/CD pipeline
- ⏳ Deploy em produção
- ⏳ Monitoramento e logging

---

## 📊 Métricas de Sucesso

### Qualidade de Código
- ✅ 0 erros TypeScript
- ✅ 0 erros PHP
- ✅ PHPStan Level 6 passing
- ✅ Build < 10 segundos
- ⏳ Test coverage > 70%

### Segurança
- ✅ OWASP Top 10 compliance
- ✅ LGPD compliance
- ✅ Rate limiting ativo
- ✅ Security headers implementados
- ✅ Audit logs funcionando

### Performance
- ✅ Dashboard < 100ms (com cache)
- ✅ Queries otimizadas (11 índices)
- ✅ Eager loading em 100% dos CRUDs
- ✅ Cache strategy implementada

### UX
- ✅ Responsivo (mobile-first)
- ✅ Loading states em todas as páginas
- ✅ Error handling consistente
- ✅ Formatação pt-BR
- ✅ Máscaras adaptativas

---

## 🎓 Aprendizados Documentados

**Decisões de Arquitetura:**
1. Stateless API com Bearer tokens (não cookies)
2. DDD + CQRS para lógica complexa
3. Repository Pattern para abstração de dados
4. Feature-first no frontend
5. Modularização de documentação

**Trade-offs:**
1. TanStack Query apenas em Dashboard (não over-engineering)
2. AuthContext mantido (padrão funcional existente)
3. Laravel Auditing vs. implementação manual (escolhido package battle-tested)
4. React Compiler compatibility (useWatch vs. watch)

**Lições Aprendidas:**
1. MySQL 9.0 requires explicit GROUP BY (only_full_group_by)
2. TypeScript verbatimModuleSyntax requires type-only imports
3. Zod schemas devem ser simples (evitar transforms complexos)
4. Docker Compose v2+ não usa `version: '3.8'`
5. Documentação modular > arquivo único gigante

---

## 📞 Contato

**GitHub:** github.com/gabriela-trevisan/sales-management-system  
**LinkedIn:** [Seu LinkedIn]  
**Email:** [Seu Email]

---

_Este projeto é um showcase de habilidades técnicas para oportunidades de carreira em desenvolvimento de software._
