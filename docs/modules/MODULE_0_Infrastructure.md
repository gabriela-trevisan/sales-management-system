# Module 0: Infraestrutura Base

**Status:** ✅ 100% Completo

---

## 📋 Visão Geral

Setup completo da infraestrutura base do projeto com Docker, Laravel, React, e banco de dados populado.

---

## 🐳 Docker Compose

### Containers Configurados (6)
- `sms_nginx` - Nginx 1.27-alpine (reverse proxy)
- `sms_backend` - PHP 8.3-fpm + Laravel 11.47
- `sms_frontend` - Node 22 + Vite 6.0
- `sms_mysql` - MySQL 9.0
- `sms_redis` - Redis 7.2
- `sms_mailhog` - Mailhog (testing emails)

### Volumes
- `mysql_data` - Persistência do banco
- `redis_data` - Persistência do cache

---

## 🏗️ Backend Setup

### Laravel 11.47
- ✅ Framework instalado e configurado
- ✅ DDD (Domain-Driven Design) architecture
- ✅ Sanctum para autenticação API
- ✅ l5-swagger para documentação
- ✅ Laravel Auditing para LGPD

### Database
- **21 Migrations criadas:**
  - Core: users, tokens, cache
  - Customers: segments, customers, addresses, contacts
  - Products: categories, products
  - Sales: pipeline_stages, opportunities, activities
  - Proposals: proposals, proposal_items
  - Performance: indexes
  - Auditing: audits (LGPD)

- **8 Seeders implementados:**
  1. UserSeeder - Admin default
  2. CustomerSegmentSeeder - 6 segmentos
  3. CustomerSeeder - 5 clientes realistas
  4. ProductCategorySeeder - 8 categorias
  5. ProductSeeder - Produtos/serviços
  6. PipelineStageSeeder - 6 estágios
  7. OpportunitySeeder - Oportunidades exemplo
  8. ProposalSeeder - Propostas exemplo

### Dados Seedados
- ✅ 1 usuário admin (admin@salesmanagement.com)
- ✅ 6 segmentos de mercado (Indústria, Financeiro, Varejo, Saúde, Logística, Educação)
- ✅ 5 clientes com endereços e contatos
- ✅ 8 categorias de produtos
- ✅ Produtos/serviços (horas dev, pacotes, suporte)
- ✅ 6 estágios do pipeline
- ✅ Oportunidades e propostas de exemplo

---

## 🎨 Frontend Setup

### React 19 + TypeScript 5.9
- ✅ Vite 6.0 configurado
- ✅ Tailwind CSS 4.1
- ✅ React Router DOM
- ✅ Axios para HTTP requests
- ✅ Lucide React para ícones

### Estrutura
```
src/
├── components/     # Componentes reutilizáveis
├── contexts/       # React Context (Auth)
├── features/       # Feature modules
├── services/       # API clients
├── utils/          # Helpers
└── types/          # TypeScript types
```

---

## ✅ Validações

```bash
✅ Docker containers: 6/6 running
✅ Backend health check: 200 OK
✅ Frontend build: SUCCESS
✅ Database populated: 5 customers, 6 segments
✅ Redis connection: OK
```

---

## 🌐 URLs

- Frontend: http://localhost:5173
- Backend: http://localhost:8000
- Swagger: http://localhost:8000/api/documentation
- Mailhog: http://localhost:8025
- MySQL: localhost:3307

---

## 🔐 Credenciais Default

**Admin User:**
- Email: admin@salesmanagement.com
- Password: password

**MySQL:**
- Host: localhost:3307
- User: root
- Password: secret
- Database: sales_management

---

_Module 0 estabelece toda a base técnica do projeto._
