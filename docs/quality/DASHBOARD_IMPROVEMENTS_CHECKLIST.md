# Dashboard Improvements - Quick Checklist

**Data:** 1 de Fevereiro de 2026  
**Status:** 📋 Planejado

---

## 🚀 Fase 1 - Quick Wins (1-2 dias)

### Visual
- [ ] Micro-indicadores de tendência nos cards (↑ +15%)
- [ ] Animações CountUp nos números
- [ ] Hover effects com elevação
- [ ] Tooltips informativos nos ícones
- [ ] Usar todas as 5 cores de chart (chart-1 a chart-5)

### Gráficos
- [ ] Gráfico de Pizza/Donut (clientes por segmento)
- [ ] Gradiente nos gráficos de linha
- [ ] Melhorar tooltips dos gráficos

### Funcionalidades
- [ ] Seletor de período mensal (01/2026, 02/2026, etc)
- [ ] Filtrar métricas por mês selecionado
- [ ] Botão de refresh manual
- [ ] DatePicker mensal (shadcn/ui)

### Backend
- [ ] Endpoint: GET /api/dashboard/customers-by-segment
- [ ] Adicionar parâmetros ?month=01&year=2026 nos endpoints existentes
- [ ] Métricas de Clientes (total, ativos, novos no mês, por segmento)
- [ ] Métricas de Produtos (mais vendidos, categorias, receita por produto)
- [ ] Métricas de Propostas (total, por status, valor total, ticket médio)
- [ ] Testes PHPUnit

### Docs
- [ ] Atualizar Swagger
- [ ] Screenshots no README

---

## 📊 Fase 2 - Média Complexidade (3-4 dias)

### Widgets
- [ ] Widget Top Performers (clientes e produtos)
- [ ] Widget Metas e Progresso
- [ ] Widget Alertas Inteligentes
- [ ] Quick Actions no header

### Gráficos
- [ ] Gráfico de Funil (pipeline conversão)
- [ ] Gráfico de Área Empilhada (timeline oportunidades)
- [ ] Zoom/Brush nos gráficos

### Backend
- [ ] Endpoint: GET /api/dashboard/funnel
- [ ] Endpoint: GET /api/dashboard/top-performers
- [ ] Endpoint: GET /api/dashboard/goals
- [ ] Endpoint: GET /api/dashboard/alerts
- [ ] Endpoint: GET /api/dashboard/opportunities-timeline
- [ ] Métricas avançadas (averageTicket, averageSaleTime, etc)
- [ ] Testes PHPUnit

### Funcionalidades
- [ ] Comparação de períodos
- [ ] Filtros na timeline
- [ ] Bibliotecas: instalar novas dependências

### Docs
- [ ] Atualizar Swagger (5 endpoints novos)
- [ ] Documentar novas métricas

---

## 🚀 Fase 3 - Alta Complexidade (5-7 dias)

### Timeline
- [ ] Agrupamento por data (Hoje, Ontem, etc)
- [ ] Filtros por tipo
- [ ] Infinite scroll ou paginação
- [ ] Avatar do usuário
- [ ] Ações rápidas (clicar abre detalhes)

### Gráficos Avançados
- [ ] Gráfico de Radar (KPIs multi-dimensionais)
- [ ] Heatmap Calendar (atividade anual)
- [ ] Reference lines (metas/médias)
- [ ] Legendas interativas (hide/show séries)

### Personalização (BAIXA PRIORIDADE)
- [ ] ~~Implementar react-grid-layout~~ (não é foco)
- [ ] ~~Drag-and-drop de widgets~~ (não é foco)
- [ ] ~~Redimensionar cards~~ (não é foco)
- [ ] ~~Ocultar/mostrar widgets~~ (não é foco)
- [ ] ~~Salvar layout (localStorage)~~ (não é foco)
- [ ] ~~Botão "Resetar Layout"~~ (não é foco)

### Exportação (ALTA PRIORIDADE)
- [ ] Instalar: html2canvas, jspdf, xlsx
- [ ] Exportar Dashboard como PDF
- [ ] Exportar gráficos individuais (PNG/SVG)
- [ ] Exportar dados como Excel/CSV
- [ ] Botão de exportação no header do Dashboard

### Real-time
- [ ] Polling a cada 5 minutos
- [ ] Indicador "última atualização"
- [ ] Botão refresh manual
- [ ] (Opcional) WebSocket para push updates

### Backend
- [ ] Endpoint: GET /api/dashboard/performance-radar
- [ ] Endpoint: GET /api/dashboard/activity-heatmap
- [ ] Endpoint: GET /api/dashboard/advanced-metrics
- [ ] Endpoint: POST /api/dashboard/layout
- [ ] Endpoint: GET /api/dashboard/export/pdf
- [ ] Testes PHPUnit completos

### Docs
- [ ] Atualizar Swagger (todos os endpoints)
- [ ] Documentação de personalização
- [ ] Documentação de exportação
- [ ] Screenshots finais
- [ ] Video demo (opcional)

---

## 📦 Instalação de Dependências

### Frontend
```bash
cd frontend
npm install html2canvas jspdf xlsx react-grid-layout react-calendar-heatmap date-fns
```

### shadcn/ui Components
```bash
npx shadcn@latest add date-picker
npx shadcn@latest add popover
npx shadcn@latest add separator
npx shadcn@latest add progress
npx shadcn@latest add alert
```

---

## 🧪 Testes

### Backend (PHPUnit)
- [ ] DashboardControllerTest - novos endpoints
- [ ] Cache tests
- [ ] Métricas calculation tests
- [ ] Performance tests

### Frontend (Jest)
- [ ] Componentes de gráficos
- [ ] Hooks customizados
- [ ] Integração com API (mock)
- [ ] Layout persistence

---

## 📝 Documentação

- [ ] Atualizar MODULE_2_Dashboard.md (✅ Feito)
- [ ] Atualizar PORTFOLIO_PROJECTS_PLAN.md (✅ Feito)
- [ ] Criar DASHBOARD_IMPROVEMENTS.md (✅ Feito)
- [ ] Atualizar Swagger (pendente)
- [ ] Screenshots e GIFs (pendente)
- [ ] README.md do projeto (pendente)

---

## 🎯 Critérios de Aceitação

### Fase 1
- Cards mostram tendência vs. mês anterior
- Animações suaves e profissionais
- Novo gráfico de segmentos funcionando
- Seletor mensal afeta todas as métricas
- Design consistente com Design System
- Métricas de Clientes, Produtos e Propostas implementadas

### Fase 2
- 5 novos endpoints funcionando
- Widgets de Top Performers, Metas e Alertas visíveis
- Gráfico de funil mostra conversão claramente
- Comparação de períodos implementada
- Testes backend passando

### Fase 3
- Timeline com agrupamento e filtros
- Exportação PDF/Excel funcionando (PRIORIDADE)
- Real-time updates operacional
- Gráficos avançados (Radar, Heatmap)
- Todos os testes passando
- Documentação completa
- (Personalização drag-and-drop: opcional, não é foco)

---

**Legenda:**
- [ ] = Não iniciado
- [x] = Concluído
- [~] = Em progresso

**Última Atualização:** 1 de Fevereiro de 2026
