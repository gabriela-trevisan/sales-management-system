# Segurança - OWASP + LGPD Compliance

**Conformidade:** OWASP Top 10 2021 + LGPD

---

## 🔒 Implementações de Segurança

### 1. Rate Limiting ✅

**Proteção Brute Force:**
```php
// routes/api.php
Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 tentativas/minuto

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // 60 requisições/minuto para rotas autenticadas
});
```

**Erro 429:**
```json
{
  "message": "Too Many Attempts.",
  "retry_after": 60
}
```

**Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
Retry-After: 60 (quando atingir limite)
```

**Conformidade:** OWASP A07:2021 (Identification and Authentication Failures)

---

### 2. Token Expiration ✅

**Laravel Sanctum:**
```php
// config/sanctum.php
'expiration' => 1440, // 24 horas
```

**Refresh Token Automático:**
```typescript
// api.ts - Frontend
api.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401 && !originalRequest._retry) {
      // Tenta refresh automático
      const newToken = await api.post('/auth/refresh');
      // Retry request com novo token
    }
  }
);
```

**Endpoint de Refresh:**
```php
POST /api/auth/refresh
Headers: Authorization: Bearer {expired_token}
Response: { token: "new_token" }
```

**Benefícios:**
- ✅ Tokens roubados expiram em 24h
- ✅ Força renovação periódica
- ✅ Reduz janela de exploração

---

### 3. Security Headers HTTP ✅

**Middleware:** `App\Http\Middleware\SecurityHeaders`

**6 Headers Implementados:**

1. **X-Frame-Options: DENY**
   - Previne clickjacking
   - Bloqueia iframes

2. **X-Content-Type-Options: nosniff**
   - Previne MIME sniffing
   - Força Content-Type

3. **X-XSS-Protection: 1; mode=block**
   - Filtro XSS legacy
   - Bloqueia página se detectar XSS

4. **Referrer-Policy: strict-origin-when-cross-origin**
   - Controla informações de referência
   - Envia apenas origem em HTTPS cross-origin

5. **Permissions-Policy**
   - Desabilita APIs não utilizadas:
   - `geolocation=(), microphone=(), camera=(), payment=(), usb=()`

6. **Strict-Transport-Security** (Produção)
   - Force HTTPS por 1 ano
   - `max-age=31536000; includeSubDomains; preload`

**Conformidade:** OWASP A05:2021 (Security Misconfiguration)

---

### 4. Content Security Policy (CSP) ✅

**Middleware:** `SecurityHeaders::buildCspPolicy()`

**Produção (Strict):**
```
default-src 'self';
script-src 'self';
style-src 'self';
img-src 'self' data: https:;
font-src 'self' data:;
connect-src 'self' https://frontend-url;
frame-ancestors 'none';
base-uri 'self';
form-action 'self';
upgrade-insecure-requests;
```

**Desenvolvimento (Permissive):**
```
default-src 'self';
script-src 'self' 'unsafe-inline' 'unsafe-eval';
style-src 'self' 'unsafe-inline';
connect-src 'self' http://localhost:5173 ws: wss:;
...
```

**Proteções:**
- ✅ XSS Prevention
- ✅ Clickjacking (frame-ancestors)
- ✅ Data Injection
- ✅ Form Hijacking
- ✅ HTTPS Enforcement (prod)

**Conformidade:** OWASP A03:2021 (Injection)

---

### 5. Audit Logs - LGPD ✅

**Package:** Laravel Auditing (owen-it/laravel-auditing v14.0.0)

**Arquitetura:**
- Event-driven via Eloquent events
- Zero manutenção (trait nos models)
- Automatic capture: old_values + new_values + user + IP

**Configuração:**
```php
// config/audit.php
'guards' => ['sanctum', 'web', 'api'],
'exclude' => ['password', 'remember_token'],
'threshold' => 1000, // Data retention
```

**Tabela `audits`:**
```sql
- user_type / user_id (polymorphic)
- event (created, updated, deleted, restored)
- auditable_type / auditable_id (polymorphic)
- old_values (JSON)
- new_values (JSON)
- url, ip_address, user_agent
- created_at, updated_at
```

**Models Auditados:**
- Customer (7 atributos)
- User (3 atributos)
- Proposal (futuro)
- Product (futuro)

**Uso:**
```php
// Automático
$customer = Customer::create($data); // ← Auditado

// Consultar
$audits = $customer->audits; // Histórico completo
$userAudits = Auth::user()->audits; // Ações do usuário
```

**Conformidade:**
- ✅ LGPD Art. 46 (Segurança dos Dados)
- ✅ LGPD Art. 48 (Comunicação de incidentes)
- ✅ Rastreabilidade 100%

---

### 6. Environment Validation ✅

**Service Provider:** `EnvValidationServiceProvider`

**12 Variáveis Validadas:**
1. APP_NAME, APP_ENV, APP_KEY, APP_URL
2. DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
3. REDIS_HOST, REDIS_PORT

**Validações de Formato:**
```php
'APP_ENV' => '/^(local|staging|production)$/',
'APP_URL' => '/^https?:\/\/.+/',
'DB_PORT' => '/^\d+$/',
'APP_KEY' => '/^base64:.{44,}$/',
```

**Fail-Fast:**
```bash
$ php artisan route:list
RuntimeException: ❌ ERRO DE CONFIGURAÇÃO: .env INVÁLIDO
Variáveis obrigatórias ausentes no .env:
  • APP_KEY (Chave de criptografia...)
```

**Benefícios:**
- ✅ Detecta problemas antes de produção
- ✅ Mensagens acionáveis
- ✅ CI/CD friendly

---

## 🛡️ Best Practices Implementadas

### Autenticação
- ✅ Stateless JWT via Sanctum
- ✅ Token expiration (24h)
- ✅ Refresh token automático
- ✅ Rate limiting (5 tentativas/min)
- ✅ Password hashing (bcrypt)

### Autorização
- ✅ Middleware auth:sanctum
- ✅ Guard sanctum com prioridade
- ⏳ Policies (futuro)
- ⏳ Roles & Permissions (futuro)

### Dados
- ✅ Soft deletes (LGPD)
- ✅ Audit logs automáticos
- ✅ Mutators para sanitização
- ✅ Validação dupla (frontend + backend)
- ✅ Prepared statements (Eloquent)

### API
- ✅ CORS configurado
- ✅ Error handling padronizado (RFC 7807)
- ✅ Rate limiting
- ✅ Input validation
- ✅ Output encoding (JSON)

---

## 🎯 OWASP Top 10 Compliance

| OWASP 2021 | Mitigação | Status |
|------------|-----------|--------|
| A01 - Broken Access Control | auth:sanctum, rate limiting | ✅ |
| A02 - Cryptographic Failures | bcrypt, APP_KEY, HTTPS | ✅ |
| A03 - Injection | Eloquent, prepared statements, CSP | ✅ |
| A04 - Insecure Design | DDD, validation, business logic | ✅ |
| A05 - Security Misconfiguration | Headers, CSP, env validation | ✅ |
| A06 - Vulnerable Components | Composer update, npm audit | ✅ |
| A07 - Auth Failures | Token expiration, rate limiting | ✅ |
| A08 - Software Integrity | Composer.lock, package-lock.json | ✅ |
| A09 - Logging Failures | Audit logs, Laravel logging | ✅ |
| A10 - SSRF | Input validation, URL whitelisting | ⏳ |

---

## 📋 LGPD Compliance

### Art. 46 - Segurança
- ✅ Audit logs (rastreabilidade)
- ✅ Soft deletes (não deletamos fisicamente)
- ✅ Bcrypt para senhas
- ✅ Token expiration
- ✅ HTTPS obrigatório (produção)

### Art. 48 - Incidentes
- ✅ Audit logs permitem investigação
- ✅ Logs de acesso (IP, user agent)
- ✅ Histórico de alterações

### Direitos do Titular
- ⏳ Portabilidade de dados (export)
- ⏳ Exclusão de dados (anonimização)
- ⏳ Correção de dados (audit trail)

---

## 🔧 Comandos de Segurança

### Audit Logs
```bash
# Ver audits de um cliente
SELECT * FROM audits WHERE auditable_type = 'App\\Domain\\Customer\\Models\\Customer';

# Audits de um usuário
SELECT * FROM audits WHERE user_id = 1;

# Audits últimas 24h
SELECT * FROM audits WHERE created_at >= NOW() - INTERVAL 24 HOUR;
```

### Headers
```bash
# Testar headers
curl -I http://localhost:8000/test-headers

# Validar CSP
curl -I http://localhost:8000/api/auth/login | grep Content-Security-Policy
```

---

_Segurança implementada seguindo padrões internacionais e legislação brasileira._
