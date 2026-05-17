# 🚀 Sales Management System

> **Portfolio Project** | Desenvolvido por **Gabriela Trevisan Leturiondo**  
> Sistema completo de gestão de vendas e CRM para estudo e demonstração de habilidades em arquitetura de software, boas práticas, segurança e tecnologias modernas.

Sistema completo de gestão de vendas e CRM desenvolvido com Laravel 11, React 19 e TypeScript.  
**Nicho:** Consultoria e Desenvolvimento de Software Customizado

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19-61DAFB?logo=react&logoColor=black)](https://react.dev)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?logo=typescript&logoColor=white)](https://typescriptlang.org)
[![Vite](https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white)](https://vitejs.dev)
[![Zod](https://img.shields.io/badge/Zod-4-3E67B1?logo=zod&logoColor=white)](https://zod.dev)
[![Documentation](https://img.shields.io/badge/Docs-PHPDoc%20%2B%20JSDoc-blue)](https://github.com)

> 📚 **Documentação Completa:** Veja [docs/STATUS.md](docs/STATUS.md) para status detalhado do projeto e navegação para módulos específicos.

---

## 📋 Sobre o Projeto

Sistema de gestão de vendas e CRM especializado para empresas de **consultoria e desenvolvimento de software**.  
Focado em gerenciar projetos customizados, alocação de equipes, propostas técnicas e comissões de equipe comercial.

- 📊 **Pipeline de Vendas Consultivo**: Prospecção → Discovery → Proposta Técnica → Negociação → Contrato → Ganho
- 🎨 **Design System Moderno**: shadcn/ui com Material Design theme (OKLCH color space)
- 🌓 **Tema Light/Dark**: Suporte completo com transições suaves e CSS variables
- 👥 **CRUD Completo de Clientes B2B**: Segmentação por setor com filtros, paginação e validação
- 📦 **CRUD Completo de Produtos**: Catálogo de serviços com categorias, preços e unidades
- ♿ **Acessibilidade**: Componentes Radix UI (WCAG 2.1 AA compliant)
- ✨ **Validação de Formulários**: react-hook-form + Zod + react-imask com máscaras dinâmicas
- 🛡️ **Validação de CPF/CNPJ**: Algoritmo com dígitos verificadores
- 🏢 **Segmentação por Setor**: Indústria, Financeiro, Varejo, Saúde, Logística, Educação
- 💼 **Catálogo de Serviços**: Horas técnicas (Arquiteto, Dev Sênior/Pleno/Júnior, QA, DevOps, UX/UI)
- 📦 **Pacotes de Projeto**: Discovery (40h), MVP (320h), Squad Dedicado (160h/mês)
- 💰 **Propostas Técnicas**: Geração e acompanhamento com escopo detalhado
- 💵 **Cálculo de Comissões**: Regras por tipo de serviço e valor de projeto
- 📈 **Dashboard Analítico**: Métricas reais, gráficos de vendas mensais e pipeline
- 📚 **API Documentada**: Swagger/OpenAPI
- 📝 **Código Documentado**: PHPDoc e JSDoc/TSDoc completos seguindo padrões oficiais
- 🔒 **Segurança de Dados**: CPF/CNPJ, telefones e CEPs armazenados sem formatação (apenas números)
- 🛡️ **LGPD Compliance**: Laravel Auditing automático (owen-it/laravel-auditing v14.0.0)
- 👥 **Responsabilidade de Clientes**: Auto-atribuição ao criador, campo protegido contra alteração
- ⚡ **Performance**: Redis cache, 11 índices de banco, queries 10-100x mais rápidas
- 🔐 **Segurança**: Rate limiting, token expiration (24h), 6 security headers (OWASP)
- 🎯 **Qualidade**: PHPStan Level 6, RFC 7807 error handling

---

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP** 8.3
- **Laravel** 11.47 (DDD Architecture)
- **Laravel Sanctum** 4.2.4 (JWT Authentication - Token 24h + Refresh)
- **Laravel Auditing** 14.0.0 (Audit automático LGPD)
- **l5-swagger** 10.1 (OpenAPI Documentation)
- **PHPStan** 2.1.36 (Static Analysis Level 6)
- **MySQL** 9.0 (11 índices de performance)
- **Redis** 7.2 (Cache de queries)
- **PHPUnit** 11.x

### Frontend
- **React** 19.2.0
- **TypeScript** 5.9.3
- **Vite** 7.2.4
- **Tailwind CSS** 4.1.18 (v4 com @tailwindcss/postcss)
- **shadcn/ui** (Material Design theme com OKLCH colors)
- **Radix UI** (Componentes acessíveis headless)
- **TanStack Query** 5.90.20 (State Management + Cache)
- **React Hook Form** 7.71.1 (Form Management)
- **Zod** 4.3.5 (Schema Validation)
- **React IMask** 7.6.1 (Input Masks)
- **Recharts** 3.6.0 (Dashboard Charts)
- **Lucide React** 0.562.0 (Icons)
- **Axios** 1.13.2 (HTTP Client + Auto Refresh Interceptor)

### Infraestrutura
- **Docker** & **Docker Compose**
- **Nginx** 1.27-alpine
- **Mailhog** (email testing)

---

## 📦 Pré-requisitos

Para rodar o projeto, você precisa ter instalado:

- [Docker](https://www.docker.com/get-started) (25.x+)
- [Docker Compose](https://docs.docker.com/compose/install/) (v2.x+)

**Ou**, para desenvolvimento local:
- PHP 8.2+ ([Download](https://www.php.net/downloads))
- Composer 2.7+ ([Download](https://getcomposer.org/download/))
- Node.js 22+ ([Download](https://nodejs.org/))
- MySQL 9.0 ou 8.4+
- Redis 7.2+

---

## 🚀 Instalação e Configuração

### ⚡ Setup Automatizado (Recomendado)

Um único comando configura todo o ambiente: cria os `.env`, constrói as imagens, aguarda os serviços ficarem prontos, instala dependências, gera a `APP_KEY`, roda migrations e seeds.

```bash
git clone https://github.com/gabriela-trevisan/sales-management-system.git
cd sales-management-system
./setup.sh
```

**Opções disponíveis:**

| Flag | Descrição |
|---|---|
| _(sem flags)_ | Instalação padrão — preserva dados existentes |
| `--fresh` | Recria banco e volumes do zero (`migrate:fresh --seed`) |
| `--no-seed` | Roda migrations sem inserir dados de exemplo |
| `--help` | Exibe ajuda |

> **Pré-requisitos:** Docker 25+ com o plugin Compose v2 (`docker compose version`).

---

### Opção 1: Usando Docker (Setup Manual)

1. **Clone o repositório:**
```bash
git clone https://github.com/gabriela-trevisan/sales-management-system.git
cd sales-management-system
```

2. **Configure o ambiente:**
```bash
# Backend
cp backend/.env.example backend/.env

# Frontend
cp frontend/.env.example frontend/.env
```

3. **Suba os containers:**
```bash
docker compose up -d
```

4. **Instale as dependências dentro do container:**
```bash
# Backend
docker compose exec backend composer install
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate --seed

# Frontend
docker compose exec frontend npm install
```

5. **Acesse a aplicação:**
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000
- **Swagger**: http://localhost:8000/api/documentation
- **Mailhog**: http://localhost:8025
- **MySQL**: localhost:3307 (user: root, password: secret)

---

### Opção 2: Desenvolvimento Local (sem Docker)

1. **Clone o repositório:**
```bash
git clone https://github.com/gabriela-trevisan/sales-management-system.git
cd sales-management-system
```

2. **Configure o Backend:**
```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

3. **Configure o Frontend:**
```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

4. **Acesse:**
- **Frontend**: http://localhost:5173
- **Backend**: http://localhost:8000

---

## 📚 Estrutura do Projeto

```
sales-management-system/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Domain/         # Camada de domínio (DDD)
│   │   │   └── Customer/  # Aggregate Root Customer
│   │   │       ├── Models/           # Customer, CustomerContact, CustomerAddress (com mutators)
│   │   │       ├── Repositories/     # CustomerRepositoryInterface
│   │   │       └── Services/         # CustomerService
│   │   ├── Application/    # Casos de uso
│   │   ├── Infrastructure/ # Implementações técnicas
│   │   │   └── Repositories/        # EloquentCustomerRepository
│   │   └── Presentation/   # Controllers e API
│   │       └── Http/
│   │           ├── Controllers/     # AuthController, CustomerController, DashboardController
│   │           └── Requests/        # CreateCustomerRequest, UpdateCustomerRequest (validações)
│   ├── database/
│   │   ├── migrations/              # 21 migrations
│   │   └── seeders/                 # 8 seeders com dados realistas
│   ├── tests/
│   └── Dockerfile
├── frontend/               # React SPA
│   ├── src/
│   │   ├── components/    # Componentes reutilizáveis
│   │   │   ├── layout/         # Layout, Sidebar, Header
│   │   │   ├── common/         # Button, Input, Alert
│   │   │   └── ConfirmDialog   # Modal de confirmação moderno
│   │   ├── contexts/      # AuthContext para gerenciamento de autenticação
│   │   ├── features/      # Features por módulo
│   │   │   ├── auth/      # LoginPage
│   │   │   ├── customers/ # CustomerListPage, CustomerFormModal
│   │   │   └── dashboard/ # DashboardPage com métricas e gráficos
│   │   ├── schemas/       # Zod schemas (customerSchema.ts)
│   │   ├── services/      # API services (customerService, segmentService, dashboardService)
│   │   ├── types/         # TypeScript interfaces e types
│   │   └── utils/         # Utilitários
│   │       ├── formatters.ts   # formatDocument, formatPhone, cleanDocument, formatCurrency, formatDate
│   │       └── validators.ts   # validateCPF, validateCNPJ, validateDocument, validatePhone, validateEmail
│   └── Dockerfile
├── docker/
│   └── nginx/             # Configurações Nginx
├── docker-compose.yml     # Orquestração de 6 containers
├── docs/                  # Documentação modular do projeto
├── PORTFOLIO_PROJECTS_PLAN.md  # Planejamento de todos os projetos do portfólio
└── README.md              # Este arquivo
```

---

## 🎨 Qualidade de Código

O projeto segue padrões de documentação reconhecidos pela indústria:

### Padrões de Documentação

**Backend (PHP):**
- 📝 **PHPDoc** - Padrão oficial PHP ([docs.phpdoc.org](https://docs.phpdoc.org/))
- Tags: `@param`, `@return`, `@throws`, `@var`, `@see`
- Integração com PHPStan, Psalm, IDEs
- Exemplos:
  ```php
  /**
   * Remove formatação do documento antes de salvar.
   * 
   * Armazena apenas números (CPF: 11 dígitos, CNPJ: 14 dígitos).
   * 
   * @param string $value Documento com ou sem formatação
   * @return void
   */
  public function setDocumentAttribute($value): void
  ```

**Frontend (TypeScript):**
- 📝 **JSDoc/TSDoc** - Padrão JavaScript/TypeScript ([jsdoc.app](https://jsdoc.app/))
- Tags: `@param`, `@returns`, `@example`, `@see`
- Type inference automático com Zod
- Exemplos:
  ```typescript
  /**
   * Valida CPF (Cadastro de Pessoas Físicas).
   * 
   * Utiliza algoritmo oficial com verificação de dígitos verificadores.
   * 
   * @param cpf - CPF com ou sem formatação
   * @returns true se válido, false caso contrário
   * 
   * @example
   * validateCPF('123.456.789-09') // true ou false
   */
  export function validateCPF(cpf: string): boolean
  ```

### Benefícios

✅ **Autocomplete inteligente** - IDEs mostram hints ao passar mouse  
✅ **Geração de docs** - phpDocumentor, TypeDoc automáticos  
✅ **Análise estática** - PHPStan, ESLint mais precisos  
✅ **Onboarding rápido** - Novos devs entendem código facilmente  
✅ **Menos bugs** - Melhor entendimento previne erros  

### Cobertura de Documentação

**Backend:** 100% dos controllers, services, models, commands e requests documentados  
**Frontend:** 100% dos services, validators, formatters e schemas documentados  

---

## 🧪 Testes

### Backend (PHPUnit)

```bash
# Com Docker
docker compose exec backend php artisan test

# Local
cd backend
php artisan test

# Com coverage
php artisan test --coverage
```

### Frontend (Vitest)

```bash
# Com Docker
docker compose exec frontend npm test

# Local
cd frontend
npm test

# Com coverage
npm run test:coverage
```

---

## 📖 Documentação da API

A documentação completa da API está disponível via Swagger/OpenAPI:
- **Swagger UI**: http://localhost:8000/api/documentation

### Endpoints Disponíveis

**Autenticação:**
- `POST /api/auth/login` - Login e geração de token JWT
- `POST /api/auth/logout` - Logout e invalidação do token
- `GET /api/auth/me` - Dados do usuário autenticado

**Dashboard:**
- `GET /api/dashboard/metrics` - Métricas gerais (clientes, oportunidades, pipeline, conversão)
- `GET /api/dashboard/recent-activities` - Últimas atividades do sistema

**Clientes:**
- `GET /api/customers` - Listar clientes com filtros e paginação
- `POST /api/customers` - Criar novo cliente
- `GET /api/customers/{id}` - Buscar cliente por ID
- `PUT /api/customers/{id}` - Atualizar cliente
- `DELETE /api/customers/{id}` - Remover cliente
- `GET /api/customer-segments` - Listar segmentos disponíveis

**Produtos:**
- `GET /api/products` - Listar produtos com filtros e paginação
- `POST /api/products` - Criar novo produto
- `GET /api/products/{id}` - Buscar produto por ID
- `PUT /api/products/{id}` - Atualizar produto
- `DELETE /api/products/{id}` - Remover produto
- `GET /api/product-categories` - Listar categorias disponíveis

**Propostas:**
- `GET /api/proposals` - Listar propostas com filtros e paginação
- `POST /api/proposals` - Criar nova proposta
- `GET /api/proposals/{id}` - Buscar proposta por ID
- `PUT /api/proposals/{id}` - Atualizar proposta
- `DELETE /api/proposals/{id}` - Remover proposta

**Credenciais de Teste:**
- Email: `admin@salesmanagement.com`
- Senha: `password`

---

## 🎯 Funcionalidades Principais

### ✅ Implementadas
- ✅ **Infraestrutura**: Docker com 6 containers (Nginx, Laravel, React, MySQL, Redis, Mailhog)
- ✅ **Autenticação JWT**: Login, logout, proteção de rotas com Laravel Sanctum
- ✅ **Layout**: Sidebar com navegação, Header com usuário e logout
- ✅ **LoginPage Redesenhado**: Layout split screen moderno com features showcase
- ✅ **Dashboard Completo**: 4 cards de métricas, LineChart de vendas mensais (Recharts), BarChart de pipeline por estágio, timeline de atividades recentes
- ✅ **CRUD de Clientes**: Listagem com filtros, criação, edição, exclusão com confirmação moderna, validação profissional e paginação
- ✅ **CRUD de Produtos**: Listagem com filtros, criação, edição, exclusão, validação dupla e paginação
- ✅ **CRUD de Propostas** (70%): Listagem, criação, edição com modal completo, gestão dinâmica de itens
- ✅ **ProposalFormModal**: Modal profissional com useFieldArray, cálculos em tempo real, auto-preenchimento de preço
- ✅ **Gestão Dinâmica de Itens**: Add/remove produtos, cálculo automático de subtotal/desconto/total
- ✅ **Validação Profissional**: react-hook-form + Zod + react-imask com máscaras dinâmicas (CPF↔CNPJ, telefone fixo↔celular)
- ✅ **Validação de CPF/CNPJ**: Algoritmo com dígitos verificadores (Receita Federal)
- ✅ **Auto-atribuição de Responsável**: Cliente automaticamente vinculado ao usuário que criou
- ✅ **Segmentação**: 6 segmentos por setor (Indústria, Financeiro, Varejo, Saúde, Logística, Educação)
- ✅ **Catálogo de Serviços**: 8 categorias, 6 tipos de unidade, SKU validado, preços e specifications
- ✅ **TypeScript Moderno**: Type-only imports com verbatimModuleSyntax, React Compiler compatible
- ✅ **Auto Refresh Token**: Interceptor Axios com renovação automática de JWT
- ✅ **DDD Architecture**: Backend organizado em Domain, Application, Infrastructure e Presentation
- ✅ **Type Safety**: TypeScript com inferência automática de tipos via Zod schemas
- ✅ **Documentação Padronizada**: PHPDoc e JSDoc/TSDoc
- ✅ **Swagger/OpenAPI**: Documentação completa de todos os endpoints
- ✅ **Performance**: Redis cache, 11 índices de banco, queries 10-100x mais rápidas
- ✅ **LGPD Compliance**: Laravel Auditing automático (owen-it/laravel-auditing v14.0.0)
- ✅ **Componentes UI Reutilizáveis**: ConfirmDialog moderno com 3 variantes (danger, warning, info)

### 🚧 Em Desenvolvimento
- 🚧 Propostas: PDF generation, email sending, versioning (30% restante)
- 🚧 Pipeline de Vendas com Kanban drag-and-drop (Module 4)

### 📋 Melhorias Planejadas do Dashboard (1 Fev 2026)

**Documentação Completa:** 
- [📊 Dashboard Improvements - Roadmap](docs/quality/DASHBOARD_IMPROVEMENTS.md)
- [✅ Dashboard Improvements - Checklist](docs/quality/DASHBOARD_IMPROVEMENTS_CHECKLIST.md)
- [📈 Dashboard Charts Analysis](docs/quality/DASHBOARD_CHARTS_ANALYSIS.md) - Análise dos melhores gráficos

#### Fase 1 - Quick Wins (1-2 dias)
- ✨ Micro-indicadores de tendência nos cards (↑ +15% vs mês anterior)
- 🎨 Usar todas as 5 cores de chart do Design System
- 📊 Gráfico de Pizza/Donut (clientes por segmento)
- 🔄 Seletor de período mensal (MM/YYYY - ex: 01/2026)
- 🎭 Animações CountUp nos números
- 📈 Novo gráfico de linha (propostas por mês)

#### Fase 2 - Média Complexidade (3-4 dias)
- 🏆 Widget Top Performers (clientes e produtos - tabelas + gráficos)
- 🎯 Widget Metas e Progresso
- 🔔 Sistema de Alertas Inteligentes
- 📊 Gráficos de Barras (Top 5 produtos e clientes)
- 📈 Comparação mensal (mês atual vs anterior)

#### Fase 3 - Alta Complexidade (5-7 dias)
- 🕒 Timeline aprimorada (agrupamento, filtros, infinite scroll)
- 📤 **Exportação completa (PDF, Excel) - ALTA PRIORIDADE**
- ⚡ Real-time updates (polling)
- 📊 Gráficos opcionais: Radar, Heatmap Calendar
- (~~Drag-and-drop: baixa prioridade, não é foco~~)

**Métricas Implementadas (Foco nos Módulos Atuais):**
- **Clientes:** Total, ativos, novos no mês, por segmento, taxa de crescimento
- **Produtos:** Total, por categoria, mais vendidos, receita por produto
- **Propostas:** Total, por status, valor total/aprovado, ticket médio, taxa de conversão

### 📋 Próximas Features (Outros Módulos)
- Gestão de Oportunidades completa
- Cálculo de comissões por serviço
- Scoring RFM de clientes
- Automação de follow-ups
- Previsão de vendas (forecast)
- Sistema de notificações

---

## 📚 Documentação

### Documentação Modular

A documentação completa do projeto está organizada em módulos para facilitar a navegação:

#### Dashboard Executivo
- **[📊 Status Geral do Projeto](docs/STATUS.md)** - Dashboard com progresso, métricas e links para documentação detalhada

#### Negócio
- **[🎯 Modelo de Negócio](docs/BUSINESS.md)** - Definição do nicho, clientes-alvo e catálogo de serviços

#### Infraestrutura
- **[🏗️ Setup Técnico](docs/INFRASTRUCTURE.md)** - Docker, stack, estrutura de pastas e configurações

#### Módulos do Sistema
- **[Module 0: Infraestrutura](docs/modules/MODULE_0_Infrastructure.md)** - Docker Compose, migrations, seeders
- **[Module 1: Autenticação](docs/modules/MODULE_1_Auth.md)** - JWT, LoginPage, Layout, PrivateRoute
- **[Module 2: Dashboard](docs/modules/MODULE_2_Dashboard.md)** - Métricas, gráficos, atividades recentes
- **[Module 3: Customers](docs/modules/MODULE_3_Customers.md)** - CRUD completo, validação CPF/CNPJ, máscaras
- **[Module 5: Products](docs/modules/MODULE_5_Products.md)** - CRUD completo, categorias, SKU unique
- **[Module 6: Proposals](docs/modules/MODULE_6_Proposals.md)** - 🆕 CRUD 70% completo, modal 573 linhas, cálculos automáticos

#### Qualidade
- **[⚡ Performance](docs/quality/PERFORMANCE.md)** - Cache Redis, índices DB, PHPStan, eager loading
- **[🔒 Segurança](docs/quality/SECURITY.md)** - OWASP, LGPD, rate limiting, headers HTTP, audit logs
- **[✨ Code Quality](docs/quality/CODE_QUALITY.md)** - PHPDoc/JSDoc, padrões, componentes reutilizáveis
- **[🎨 Design System](docs/quality/DESIGN_SYSTEM.md)** - Material Design, OKLCH colors, shadcn/ui
- **[📊 Dashboard Improvements](docs/quality/DASHBOARD_IMPROVEMENTS.md)** - 🆕 Roadmap de melhorias planejadas
- **[✅ Dashboard Checklist](docs/quality/DASHBOARD_IMPROVEMENTS_CHECKLIST.md)** - 🆕 Checklist de implementação
- **[📈 Dashboard Charts Analysis](docs/quality/DASHBOARD_CHARTS_ANALYSIS.md)** - 🆕 Análise dos melhores gráficos

#### Guias
- **[🔧 Comandos Úteis](docs/guides/COMMANDS.md)** - Docker, Artisan, NPM, MySQL, Redis, troubleshooting
- **[📖 Regras de Desenvolvimento](docs/guides/DEVELOPMENT_RULES.md)** - Convenções, objetivos, roadmap, métricas

---

## 🤝 Contribuição

Este é um projeto de portfólio pessoal. Se você encontrou algum problema ou tem sugestões, sinta-se à vontade para abrir uma issue.

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👨‍💻 Autor

**Gabriela Trevisan**

- GitHub: [@gabriela-trevisan](https://github.com/gabriela-trevisan)
- LinkedIn: [Seu LinkedIn](https://linkedin.com/in/seu-perfil)

---

**⭐ Se este projeto foi útil para você, considere dar uma estrela no repositório!**
