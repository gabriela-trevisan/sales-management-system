# Plano de Portfólio - Projetos GitHub

**Data:** 7 de Janeiro de 2026  
**Objetivo:** Projeto pessoal no GitHub para incrementar portfólio demonstrando conhecimento em sistemas coorporativos de vendas, fiscal, administrativo e logística.

## 🎯 Estratégia de Desenvolvimento

### Ordem Recomendada dos Projetos

1. **Vendas** (primeiro)
   - Core de qualquer negócio
   - Mostra valor imediato para potenciais empregadores

2. **Administrativo** (segundo)
   - Complementa vendas naturalmente
   - Gestão de clientes, pagamentos, contratos

3. **Fiscal** (terceiro)
   - Integra com os anteriores
   - Demonstra conhecimento técnico complexo
   - Diferencial no mercado

4. **Logística** (último)
   - Área mais específica
   - Domínio bem diferente das outras

---

## 💻 Stack Tecnológica

### Primária
- PHP / Laravel
- JavaScript
- MySQL
- PHPUnit
- Docker

### Secundária
- Node.js
- React
- Next.js
- Jest

### Complementar
- Python (quando fizer sentido)

---

## 🚀 Distribuição dos Projetos

### Projeto 1: Sales Management System
**Área:** Vendas / CRM  
**Nicho:** Consultoria e Desenvolvimento de Software Customizado

**Stack Tecnológica:**
- **Backend:** PHP/Laravel 11 (API RESTful + DDD)
- **Frontend:** React 19 + TypeScript + Vite + shadcn/ui
- **Design System:** Material Design theme (OKLCH color space)
- **Banco de Dados:** MySQL 9.0
- **Testes:** PHPUnit
- **Infraestrutura:** Docker
- **Documentação:** Swagger/OpenAPI

**Features Principais:**
- ✅ Pipeline de vendas com 6 estágios (Prospecção → Discovery → Proposta Técnica → Negociação → Contrato → Ganho)
- ✅ Dashboard com métricas reais e gráficos (Recharts)
- ✅ CRUD Completo de Clientes com filtros, paginação, validação e segmentação por setor
- ✅ Gestão de clientes segmentados por setor (Indústria, Financeiro, Varejo, Saúde, Logística, Educação)
- ✅ Catálogo de serviços (Horas técnicas: Arquiteto, Dev Sênior/Pleno/Júnior, QA, DevOps, UX/UI)
- ✅ Pacotes de projetos (Discovery, MVP, Squad Dedicado)
- ✅ **CRUD Completo de Produtos** (Categorias, preços, unidades, soft deletes, validação dupla)
- 📋 **Melhorias do Dashboard** (Planejado - 1 Fev 2026)
  - Seletor de período mensal (01/2026)
  - Micro-indicadores de tendência nos cards (vs. mês anterior)
  - Novos gráficos: Pizza/Donut (segmentos), Line (propostas/mês), Barras (Top 5)
  - Widgets: Top Performers, Metas e Progresso, Alertas Inteligentes
  - Comparação mensal
  - **Exportação (PDF, Excel) - ALTA PRIORIDADE**
  - Métricas focadas: Clientes, Produtos e Propostas
  - (Drag-and-drop: baixa prioridade, não é foco)
  - Ver detalhes em: `docs/modules/MODULE_2_Dashboard.md` seção "Melhorias Planejadas"
  - Análise de gráficos: `docs/quality/DASHBOARD_CHARTS_ANALYSIS.md`
- ⏳ Kanban board com drag-and-drop para pipeline visual
- ⏳ Gestão de propostas técnicas com PDF
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
- Arquitetura DDD (Domain-Driven Design) no backend
- API documentada com Swagger
- Nicho especializado em consultoria de software (demonstra conhecimento do mercado)
- Produtos realistas: horas técnicas, pacotes de projeto, suporte
- Dashboard com dados reais do banco (não mockado)
- **Segurança de dados**: CPF/CNPJ, telefones e CEPs armazenados sem formatação (apenas números)
- **Sanitização automática**: Mutators nos Models para limpeza de dados
- **LGPD Compliance**: Soft deletes implementado

---

### Projeto 2: Financial Admin System
**Área:** Administrativo / Financeiro

**Stack Tecnológica:**
- **Backend:** Node.js/Express (API REST)
- **Frontend:** Next.js (SSR/SSG)
- **Banco de Dados:** PostgreSQL
- **Testes:** Jest
- **Infraestrutura:** Docker

**Features Principais:**
- Dashboard financeiro
- Contas a receber/pagar
- Análise de crédito
- Conciliação bancária
- Gestão de contratos
- Condições de pagamento

**Diferenciais:**
- Demonstra versatilidade (Node.js)
- SSR ideal para dashboards e relatórios
- Performance em visualização de dados

---

### Projeto 3: Fiscal Compliance System
**Área:** Fiscal / Tributário

**Stack Tecnológica:**
- **Backend:** PHP/Laravel (regras fiscais)
- **Frontend:** Vue.js
- **Microserviço:** Python (cálculos e validações)
- **Banco de Dados:** MySQL
- **Testes:** PHPUnit + pytest
- **Infraestrutura:** Docker
- **Arquitetura:** Polyrepo (2 repositórios)

**Repositórios:**
1. `fiscal-compliance-system` - Laravel + Vue.js
2. `fiscal-calculator-service` - Python Microserviço (FastAPI)

**Features Principais:**
- Emissão de NFe (ambiente de homologação)
- Cálculo de impostos (ICMS, IPI, PIS, COFINS, ST)
- SPED Fiscal
- Validações fiscais complexas
- Exceções fiscais por cliente/produto
- Compliance tributário

**Diferenciais:**
- Arquitetura de microserviços **real** (polyrepo)
- Python para cálculos pesados (serviço independente)
- Comunicação REST entre serviços
- Deploy e escalabilidade independente
- Conhecimento técnico complexo
- Alto valor de mercado

---

### Projeto 4: Logistics WMS System
**Área:** Logística / WMS

**Stack Tecnológica:**
- **Backend:** PHP/Laravel
- **Frontend:** Alpine.js + Livewire
- **Banco de Dados:** MySQL
- **Testes:** PHPUnit
- **Infraestrutura:** Docker

**Features Principais:**
- WMS (Warehouse Management System)
- Gestão de estoque
- Separação e expedição
- Roteirização de entregas
- Rastreabilidade de produtos
- Inventário

**Diferenciais:**
- Tempo real (Livewire)
- Foco no domínio complexo
- Desenvolvimento ágil

---

## 🎁 Projeto Bônus (Opcional)

### Projeto 5: Business Intelligence Analytics
**Área:** BI / Analytics

**Stack Tecnológica:**
- **Backend:** Python/FastAPI
- **Frontend:** React
- **Banco de Dados:** PostgreSQL
- **Testes:** pytest + Jest
- **Infraestrutura:** Docker

**Features Principais:**
- Dashboard de BI consolidado
- Análises preditivas (vendas, inadimplência)
- Integração com outros sistemas
- Relatórios gerenciais
- Machine Learning básico

**Diferenciais:**
- Integração entre sistemas
- Python para análise de dados
- Visão estratégica do negócio

---

## ✅ Vantagens da Distribuição Proposta

### Técnicas
- ✅ PHP/Laravel em 3 projetos (expertise principal)
- ✅ React/Next em 2 projetos (frontend moderno)
- ✅ Node.js em 1 projeto completo (versatilidade fullstack)
- ✅ Python posicionado estrategicamente
- ✅ Docker em todos os projetos
- ✅ Testes automatizados em todos
- ✅ Demonstra arquitetura de microserviços (Projeto 3)
- ✅ Monorepo em 4 projetos (padrão full-stack moderno)
- ✅ Polyrepo em 1 projeto (arquitetura distribuída)

### Negócio
- ✅ Cobertura completa de sistemas empresariais
- ✅ Demonstra conhecimento de domínios complexos
- ✅ Portfolio progressivo (fácil → complexo)
- ✅ Atrativo para diferentes tipos de empresas

### Portfólio
- ✅ Variedade tecnológica
- ✅ Profundidade em áreas específicas
- ✅ Projetos independentes e reutilizáveis
- ✅ Demonstra evolução e aprendizado

---

## 📝 Boas Práticas para Cada Projeto

### Documentação
- README detalhado com:
  - Descrição do projeto
  - Tecnologias utilizadas
  - Como rodar (Docker)
  - Screenshots/GIFs
  - Arquitetura do sistema
  - Regras de negócio documentadas

### Código
- Padrões de projeto (Repository, Service, etc)
- SOLID principles
- Clean Code
- Comentários em lógicas complexas
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
- Testes unitários
- Testes de integração
- Coverage report
- CI/CD básico (GitHub Actions)

---

## 🎯 Próximos Passos

1. Criar repositório do primeiro projeto (Sales Management System)
2. Estruturar boilerplate Laravel + React
3. Configurar Docker Compose
4. Definir schema do banco de dados
5. Implementar features core
6. Documentar e publicar

---

## 📌 Notas Importantes

- Focar em qualidade, não quantidade
- Melhor 2-3 projetos bem feitos do que 5 incompletos
- Manter código limpo e bem documentado
- Usar dados fictícios realistas
- Demonstrar evolução do conhecimento
- Cada projeto deve ser utilizável (não apenas código exemplo)

---

## 🔗 Links Úteis (Para Adicionar Futuramente)

- [ ] Repositório Sales Management System
- [ ] Repositório Financial Admin System
- [ ] Repositório Fiscal Compliance System
- [ ] Repositório Logistics WMS System
- [ ] Repositório Business Intelligence Analytics

---

**Observações:** Este documento deve ser atualizado conforme o desenvolvimento dos projetos progride. Incluir links para os repositórios, decisões arquiteturais importantes e aprendizados durante o desenvolvimento.
