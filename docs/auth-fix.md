# Correção da Autenticação — SPA Cookie Auth (Sanctum Stateful)

> ✅ **Resolvido em 15/05/2026** — Todos os itens abaixo foram implementados e validados.

## Contexto

O sistema foi projetado para **Sanctum SPA Cookie Authentication** — a abordagem mais segura para SPAs conforme OWASP ASVS v4 §3.4.

**Estado atual (corrigido):**
- `statefulApi()` restaurado em `bootstrap/app.php`
- `SESSION_DOMAIN=` (vazio), `SANCTUM_STATEFUL_DOMAINS`, `SESSION_SECURE_COOKIE` configurados no `.env`
- `login()` retorna apenas `{ user }` sem token — sessão httpOnly via cookie
- Vite proxy configurado — elimina cross-origin entre frontend e backend no browser
- Novo endpoint `POST /api/auth/token` para clientes não-SPA (Swagger, mobile)

---

## Causa raiz do 419 original (e da regressão)

**419 original:** `statefulApi()` removido do `bootstrap/app.php` — sem ele, o `ValidateCsrfToken` nunca é ativado para a sessão, mas o `VerifyCsrfToken` padrão do Laravel ainda opera em certas condições.

**419 após restaurar `statefulApi()`:** Mesmo com o middleware correto, o CSRF falha em ambiente Docker cross-origin (`localhost:5173` → `localhost:8000`) porque:
- O cookie `XSRF-TOKEN` é setado com `Domain=localhost` (ou sem domain)
- Browsers modernos (Chrome, Firefox) têm restrições `SameSite` que impedem o envio do cookie entre origens diferentes na mesma máquina
- O axios lia o cookie `XSRF-TOKEN` de `localhost:8000`, mas o enviava em requests para `localhost:5173` — origins diferentes

**Solução definitiva: Vite Dev Server Proxy.** Todas as chamadas saem do mesmo origin (`localhost:5173`). O browser não vê nenhuma diferença de porta ou host — é same-origin do início ao fim.


- Novo endpoint `/api/auth/token` para clientes não-SPA (Swagger UI, mobile)

---

## Diagnóstico: por que o 419 aconteceu originalmente?

O `statefulApi()` **não era o problema**. O 419 era causado pelo `EnvValidationServiceProvider` quebrando o boot antes do middleware de sessão inicializar o cookie CSRF. Isso já foi corrigido (provider agora pula validação quando `configurationIsCached()` retorna `true`).

---

## Passo a passo da correção

### ✅ 1. Restaurar `statefulApi()` no `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    // Sanctum SPA Cookie Auth — adiciona EncryptCookies, StartSession,
    // ValidateCsrfToken para requisições vindas de SANCTUM_STATEFUL_DOMAINS
    $middleware->statefulApi();

    // Security Headers para todas as requisições
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
})
```

### ✅ 2. Configurar `SANCTUM_STATEFUL_DOMAINS` e `SESSION_SECURE_COOKIE` no `.env`

```dotenv
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost
SESSION_DOMAIN=            # vazio = sem atributo Domain no cookie (correto para proxy)
SESSION_SECURE_COOKIE=false   # true em produção (HTTPS obrigatório)
```

### ✅ 3. Configurar Vite proxy — eliminar cross-origin no browser

**Raiz do 419 pós-`statefulApi()`:** frontend em `localhost:5173` chamava backend em `localhost:8000` — cross-origin. O cookie `XSRF-TOKEN` setado por uma origem não é confiável para outra origem em browsers modernos (restrições `SameSite`).

**Solução:** Vite Dev Server proxy em `vite.config.ts`:

```ts
proxy: {
  '/api': { target: env.VITE_BACKEND_PROXY_TARGET, changeOrigin: true },
  '/sanctum': { target: env.VITE_BACKEND_PROXY_TARGET, changeOrigin: true },
}
```

`VITE_BACKEND_PROXY_TARGET`:
- **Docker:** `http://nginx:8000` (container nginx na rede Docker)
- **Local sem Docker:** `http://localhost:8000`

`VITE_API_URL=/api` → URL relativa, sem origin hardcoded.

O browser vê tudo como `localhost:5173` — same-origin, CSRF funciona.

### ✅ 4. Corrigir `AuthController::login()` — remover token do body (SPA-only)

Token removido do body. `login()` é exclusivo para SPA Cookie Auth.  
Para clientes não-SPA (Swagger, mobile), usar o novo endpoint `POST /api/auth/token`.

### ✅ 5. Criar `AuthController::token()` — endpoint para clientes não-SPA

Novo endpoint `POST /api/auth/token` gera Bearer token para Swagger UI e clientes não-SPA.  
Rate limiting aplicado: 5 tentativas/minuto.

### ✅ 6. `AuthController::logout()` — invalidar sessão

Já implementado: `auth()->guard('web')->logout()`, `session()->invalidate()`, `session()->regenerateToken()`.

### ✅ 7. `AuthController::me()` — guard correto

Já implementado: `$request->user()` resolve tanto cookie session quanto Bearer token via `auth:sanctum`.

### ✅ 8. `cors.php` — já estava correto

`supports_credentials: true` + `allowed_origins` restrito a `APP_FRONTEND_URL`.

> ⚠️ `supports_credentials: true` **nunca** pode ser combinado com `allowed_origins: ['*']` — viola a spec CORS e é rejeitado pelo browser.

### ✅ 9. `api.ts` no frontend — já estava correto

`withCredentials: true` + `initCsrf()` antes do login.

### ✅ 10. Cache regenerado após as alterações

```bash
docker compose exec backend php artisan optimize:clear
docker compose exec backend php artisan config:cache
```

---

## Fluxo completo (implementado)

```
1. Frontend → GET /sanctum/csrf-cookie
   └─ Backend define XSRF-TOKEN (cookie legível JS) + laravel_session (httpOnly)
      ↑ Vite proxy: browser chama localhost:5173/sanctum/... → nginx:8000 internamente

2. Frontend → POST /api/auth/login  { email, password }
   └─ Axios lê XSRF-TOKEN do cookie e envia X-XSRF-TOKEN header automaticamente
   └─ Vite proxy encaminha localhost:5173/api/... → nginx:8000/api/...
   └─ Backend valida CSRF ✓ (same-origin do ponto de vista do browser)
   └─ Backend autentica, regenera session ID (previne session fixation)
   └─ Response: { user } — sem token no body

3. Frontend → GET /api/auth/me (e demais rotas)
   └─ Browser envia laravel_session automaticamente (httpOnly, sem acesso JS)
   └─ Backend resolve usuário via Sanctum ✓

4. Frontend → POST /api/auth/logout
   └─ Backend invalida sessão + regenera CSRF token
   └─ Cookie expira

5. Swagger/mobile → POST /api/auth/token  { email, password }
   └─ Endpoint dedicado para não-SPA — retorna Bearer token
```

---

## O que esta abordagem protege (OWASP)

| Ameaça | Proteção |
|---|---|
| **XSS rouba credencial** | Cookie `httpOnly` → inacessível a qualquer script |
| **CSRF** | XSRF-TOKEN validado em toda requisição não-idempotente |
| **Session fixation** | `session()->regenerate()` no login |
| **Token leak no body** | Token não retornado para o SPA (endpoint separado `/auth/token`) |
| **CORS wildcard** | `allowed_origins` restrito ao frontend URL |
| **Cross-origin cookie bypass** | Vite proxy: same-origin no browser, SameSite cookies funcionam corretamente |
