# Infraestrutura Técnica - Sales Management System

---

## 🐳 Docker Compose

### Containers (6)

| Container | Imagem | Porta | Descrição |
|-----------|--------|-------|-----------|
| `sms_nginx` | nginx:1.27-alpine | 8000 | Reverse proxy |
| `sms_backend` | php:8.3-fpm | - | Laravel API |
| `sms_frontend` | node:22-alpine | 5173 | React + Vite |
| `sms_mysql` | mysql:9.0 | 3307 | Banco de dados |
| `sms_redis` | redis:7.2-alpine | - | Cache + sessions |
| `sms_mailhog` | mailhog/mailhog | 8025 | Email testing |

### Volumes
- `mysql_data` - Dados persistentes do MySQL
- `redis_data` - Dados persistentes do Redis

### Networks
- `app-network` - Rede interna dos containers

---

## 🛠️ Stack Tecnológica

### Backend
- **Framework:** Laravel 11.47
- **Linguagem:** PHP 8.3
- **Banco de Dados:** MySQL 9.0
- **Cache:** Redis 7.2
- **Documentação:** Swagger/OpenAPI (l5-swagger 10.1)

### Frontend
- **Framework:** React 19
- **Linguagem:** TypeScript 5.9
- **Build Tool:** Vite 6.0
- **Estilização:** Tailwind CSS 4.1
- **State Management:** TanStack Query v5
- **Formulários:** react-hook-form 7.71 + Zod 4.3
- **Ícones:** Lucide React 0.562

### DevOps
- **Containerização:** Docker + Docker Compose v2+
- **Web Server:** Nginx 1.27
- **Análise Estática:** PHPStan 2.1.33 (Level 6)

---

## 📁 Estrutura do Projeto

```
sales-management-system/
├── backend/               # Laravel API
│   ├── app/
│   │   ├── Domain/       # Modelos de domínio
│   │   ├── Application/  # Use Cases (CQRS)
│   │   ├── Infrastructure/  # Repositories
│   │   └── Presentation/    # Controllers + Resources
│   ├── database/
│   │   ├── migrations/   # 21 migrations
│   │   └── seeders/      # 8 seeders
│   └── routes/
│       └── api.php       # Rotas da API
│
├── frontend/             # React SPA
│   └── src/
│       ├── components/   # Componentes reutilizáveis
│       ├── features/     # Feature modules
│       ├── contexts/     # React Context
│       └── services/     # API clients
│
├── docker/              # Configs Docker
│   └── nginx/
│       └── default.conf
│
├── docs/                # Documentação modular 🆕
│   ├── modules/
│   ├── quality/
│   └── guides/
│
└── docker-compose.yml   # Orquestração
```

---

## 🗄️ Database

### Migrations (21)

**Core:**
- `2014_10_12_000000_create_users_table.php`
- `2019_12_14_000001_create_personal_access_tokens_table.php`
- `2024_12_27_000000_create_cache_table.php`

**Customers:**
- `2026_01_07_140835_create_customer_segments_table.php`
- `2026_01_07_140836_create_customers_table.php`
- `2026_01_07_140837_create_customer_addresses_table.php`
- `2026_01_07_140838_create_customer_contacts_table.php`

**Products:**
- `2026_01_07_141038_create_product_categories_table.php`
- `2026_01_07_141040_create_products_table.php`

**Sales:**
- `2026_01_07_141642_create_pipeline_stages_table.php`
- `2026_01_07_141643_create_opportunities_table.php`
- `2026_01_07_141644_create_opportunity_activities_table.php`

**Proposals:**
- `2026_01_08_150000_create_proposals_table.php`
- `2026_01_08_150001_create_proposal_items_table.php`

**Performance:**
- `2026_01_17_183854_add_performance_indexes_v2.php` (11 índices)

**Auditing:**
- `2026_01_21_132022_create_audits_table.php` (LGPD)

### Seeders (8)

1. **UserSeeder** - Usuário admin default
2. **CustomerSegmentSeeder** - 6 segmentos de mercado
3. **CustomerSeeder** - 5 clientes realistas
4. **ProductCategorySeeder** - 8 categorias
5. **ProductSeeder** - Produtos/serviços (horas dev, pacotes)
6. **PipelineStageSeeder** - 6 estágios do pipeline
7. **OpportunitySeeder** - Oportunidades de exemplo
8. **ProposalSeeder** - Propostas de exemplo

---

## 🔐 Autenticação

### Laravel Sanctum
- **Tipo:** Stateless API tokens (Bearer)
- **Token Expiration:** 24 horas
- **Refresh Token:** Endpoint `/api/auth/refresh`
- **Guards:** sanctum (prioridade)

### Endpoints de Auth
- `POST /api/auth/login` - Autenticar e receber token
- `POST /api/auth/logout` - Revogar token atual
- `GET /api/auth/me` - Dados do usuário autenticado
- `POST /api/auth/refresh` - Renovar token expirado

---

## 🚀 Build & Deploy

### Comandos Docker

```bash
# Iniciar todos os containers
docker-compose up -d

# Ver logs
docker-compose logs -f <service>

# Parar tudo
docker-compose down

# Rebuild completo
docker-compose up -d --build

# Executar comando no container
docker-compose exec <service> <command>
```

### Build Frontend
```bash
docker exec sms_frontend npm run build
# Output: dist/ com 3 arquivos otimizados
# Tempo: ~8.52s
```

### Build Backend
```bash
docker exec sms_backend php artisan config:cache
docker exec sms_backend php artisan route:cache
docker exec sms_backend php artisan view:cache
```

---

## 📊 Performance

### Cache Strategy
- **Dashboard Metrics:** 5 minutos (Redis)
- **Customer Segments:** 15 minutos (Redis)
- **Invalidação:** Automática em CUD operations

### Database Indexes (11)
- Customers: 4 índices (status, document, email, created_at)
- Opportunities: 3 índices (pipeline, value, expected_close)
- Addresses: 1 índice (zipcode)
- Contacts: 1 índice (email)

### Estimativa de Performance
- 1k registros: 50ms → 5ms (10x)
- 10k registros: 500ms → 50ms (10x)
- 100k registros: 5s → 200ms (25x)

---

## 🌐 URLs e Portas

| Serviço | URL | Porta |
|---------|-----|-------|
| Frontend | http://localhost:5173 | 5173 |
| Backend API | http://localhost:8000 | 8000 |
| Swagger | http://localhost:8000/api/documentation | 8000 |
| Mailhog | http://localhost:8025 | 8025 |
| MySQL | localhost:3307 | 3307 |
| Redis | localhost:6379 | 6379 |

### Credenciais

**MySQL:**
- Host: localhost
- Port: 3307
- User: root
- Password: secret
- Database: sales_management

**Admin User:**
- Email: admin@salesmanagement.com
- Password: password

---

## 📝 Notas Técnicas

### Docker Compose v2+
- ✅ Não usa `version: '3.8'` (obsoleto)
- ✅ Sintaxe moderna

### TypeScript
- ✅ `verbatimModuleSyntax` enabled
- ✅ Type-only imports com `import type`

### API Stateless
- ✅ Bearer token authentication
- ✅ Sem cookies/sessions para auth
- ✅ CORS configurado para frontend local
- ✅ CSRF protection desabilitado para API

---

_Este documento detalha a configuração técnica e infraestrutura do projeto._
