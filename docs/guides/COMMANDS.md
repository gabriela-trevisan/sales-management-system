# Comandos Úteis

> **Nota:** Use `docker compose` (com espaço, plugin v2). O binário legado `docker-compose` (v1) foi descontinuado.

---

## 🐳 Docker

### Gerenciamento de Containers

```bash
# Iniciar todos os containers
docker compose up -d

# Parar todos os containers
docker compose down

# Parar e remover volumes
docker compose down -v

# Rebuild completo
docker compose up -d --build

# Ver status dos containers
docker compose ps

# Ver logs
docker compose logs -f

# Ver logs de um container específico
docker compose logs -f backend
docker compose logs -f frontend

# Restart de um container
docker compose restart backend
```

### Acessar Containers

```bash
# Backend (bash)
docker compose exec backend bash

# Frontend (sh - Alpine Linux)
docker compose exec frontend sh

# MySQL
docker compose exec mysql mysql -u root -p

# Redis CLI
docker compose exec redis redis-cli
```

---

## 🔧 Backend (Laravel)

### Artisan

```bash
# Migrations
docker compose exec backend php artisan migrate
docker compose exec backend php artisan migrate:fresh
docker compose exec backend php artisan migrate:fresh --seed
docker compose exec backend php artisan migrate:rollback
docker compose exec backend php artisan migrate:status

# Seeders
docker compose exec backend php artisan db:seed
docker compose exec backend php artisan db:seed --class=CustomerSeeder

# Cache
docker compose exec backend php artisan cache:clear
docker compose exec backend php artisan config:cache
docker compose exec backend php artisan route:cache
docker compose exec backend php artisan view:cache

# Limpar todos os caches
docker compose exec backend php artisan optimize:clear

# Rotas
docker compose exec backend php artisan route:list
docker exec sms_backend php artisan route:list --path=customers
docker exec sms_backend php artisan route:list --path=proposals

# Swagger
docker exec sms_backend php artisan l5-swagger:generate

# Tinker (REPL)
docker exec -it sms_backend php artisan tinker
```

### Composer

```bash
# Instalar dependências
docker exec sms_backend composer install

# Adicionar pacote
docker exec sms_backend composer require vendor/package

# Atualizar dependências
docker exec sms_backend composer update

# Dump autoload
docker exec sms_backend composer dump-autoload
```

### PHPStan

```bash
# Análise completa
docker exec sms_backend vendor/bin/phpstan analyse --memory-limit=512M

# Análise específica
docker exec sms_backend vendor/bin/phpstan analyse app/Domain/Customer

# Clear cache PHPStan
docker exec sms_backend vendor/bin/phpstan clear-result-cache
```

### Testes

```bash
# Rodar todos os testes
docker exec sms_backend php artisan test

# Rodar testes específicos
docker exec sms_backend php artisan test --filter CustomerTest

# Testes com coverage
docker exec sms_backend php artisan test --coverage
```

### Logs

```bash
# Ver últimas 100 linhas
docker exec sms_backend tail -f storage/logs/laravel.log

# Limpar logs
docker exec sms_backend truncate -s 0 storage/logs/laravel.log
```

---

## ⚛️ Frontend (React)

### NPM

```bash
# Instalar dependências
docker exec sms_frontend npm install

# Adicionar pacote
docker exec sms_frontend npm install <pacote>
docker exec sms_frontend npm install <pacote> --save-dev

# Remover pacote
docker exec sms_frontend npm uninstall <pacote>

# Atualizar dependências
docker exec sms_frontend npm update

# Audit de segurança
docker exec sms_frontend npm audit
docker exec sms_frontend npm audit fix
```

### Build & Dev

```bash
# Build de produção
docker exec sms_frontend npm run build

# Dev mode (já roda automaticamente no container)
docker exec sms_frontend npm run dev

# Lint
docker exec sms_frontend npm run lint

# Preview do build
docker exec sms_frontend npm run preview
```

### TypeScript

```bash
# Type checking
docker exec sms_frontend npx tsc --noEmit

# Watch mode
docker exec sms_frontend npx tsc --noEmit --watch
```

---

## 🗄️ Database (MySQL)

### Acesso Direto

```bash
# MySQL CLI
docker exec -it sms_mysql mysql -u root -p
# Password: secret

# Dump database
docker exec sms_mysql mysqldump -u root -p sales_management > backup.sql

# Restaurar database
docker exec -i sms_mysql mysql -u root -p sales_management < backup.sql
```

### Queries Úteis

```sql
-- Ver todas as tabelas
SHOW TABLES;

-- Estrutura de uma tabela
DESCRIBE customers;

-- Ver índices
SHOW INDEX FROM customers;

-- Estatísticas de tabelas
SELECT 
    table_name,
    table_rows,
    data_length,
    index_length
FROM information_schema.tables
WHERE table_schema = 'sales_management';

-- Audits recentes
SELECT * FROM audits ORDER BY created_at DESC LIMIT 10;

-- Clientes por segmento
SELECT cs.name, COUNT(*) as total
FROM customers c
JOIN customer_segments cs ON c.segment_id = cs.id
GROUP BY cs.id, cs.name;
```

---

## 🔴 Redis

### Redis CLI

```bash
# Acessar Redis CLI
docker exec -it sms_redis redis-cli

# Ver todas as keys
KEYS *

# Ver valor de uma key
GET dashboard.metrics

# Deletar key
DEL dashboard.metrics

# Ver TTL de uma key
TTL dashboard.metrics

# Flush all
FLUSHALL

# Info
INFO stats
INFO memory

# Monitorar comandos em tempo real
MONITOR
```

---

## 🧪 Testing

### cURL Tests

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@salesmanagement.com","password":"password"}'

# Get customers (autenticado)
curl -H "Authorization: Bearer {token}" \
  http://localhost:8000/api/customers

# Create customer
curl -X POST http://localhost:8000/api/customers \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com",...}'
```

---

## 📊 Monitoring

### Docker Stats

```bash
# Uso de recursos em tempo real
docker stats

# Stats de um container específico
docker stats sms_backend
```

### Disk Usage

```bash
# Ver uso de disco do Docker
docker system df

# Limpar recursos não utilizados
docker system prune

# Limpar tudo (cuidado!)
docker system prune -a --volumes
```

---

## 🔧 Troubleshooting

### Rebuild Completo

```bash
# Parar tudo
docker-compose down -v

# Rebuild
docker-compose build --no-cache

# Subir
docker-compose up -d

# Reinstalar dependências
docker exec sms_backend composer install
docker exec sms_frontend npm install

# Migrations + seed
docker exec sms_backend php artisan migrate:fresh --seed

# Gerar Swagger
docker exec sms_backend php artisan l5-swagger:generate
```

### Permissões (Linux)

```bash
# Corrigir permissões storage
docker exec sms_backend chmod -R 775 storage bootstrap/cache
docker exec sms_backend chown -R www-data:www-data storage bootstrap/cache
```

### Limpar Tudo

```bash
# Backend
docker exec sms_backend php artisan optimize:clear
docker exec sms_backend composer dump-autoload

# Frontend
docker exec sms_frontend rm -rf node_modules package-lock.json
docker exec sms_frontend npm install

# Docker
docker-compose down -v
docker volume prune
docker system prune -a
```

---

## 🌐 URLs de Acesso

```bash
Frontend:   http://localhost:5173
Backend:    http://localhost:8000
Swagger:    http://localhost:8000/api/documentation
Mailhog:    http://localhost:8025
MySQL:      localhost:3307 (DBeaver/WorkBench)
```

---

_Referência rápida para desenvolvimento diário._
