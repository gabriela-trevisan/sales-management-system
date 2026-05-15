# Correção da Autenticação — SPA Cookie Auth (Sanctum Stateful)

## Contexto

O sistema foi projetado para **Sanctum SPA Cookie Authentication** — a abordagem mais segura para SPAs conforme OWASP ASVS v4 §3.4.  
No entanto, a implementação atual está inconsistente:

- O `statefulApi()` foi removido do `bootstrap/app.php` (para resolver um 419 sintomático)
- O frontend chama `initCsrf()` e usa `withCredentials: true`, esperando cookie auth
- O backend cria sessão E token, mas retorna o token no body (que o frontend ignora)
- `AuthContext` armazena `user` no `localStorage` (ok — são dados públicos, não o token)

**Resultado:** login retorna 200, mas rotas autenticadas retornam 401 — não há sessão ativa e não há Bearer token sendo enviado.

---

## Diagnóstico: por que o 419 aconteceu originalmente?

O `statefulApi()` **não era o problema**. O 419 era causado pelo `EnvValidationServiceProvider` quebrando o boot antes do middleware de sessão inicializar o cookie CSRF. Isso já foi corrigido (provider agora pula validação quando `configurationIsCached()` retorna `true`).

---

## Passo a passo da correção

### 1. Restaurar `statefulApi()` no `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    // Sanctum SPA Cookie Auth — adiciona EncryptCookies, StartSession,
    // ValidateCsrfToken para requisições vindas de SANCTUM_STATEFUL_DOMAINS
    $middleware->statefulApi();

    // Security Headers para todas as requisições
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
})
```

### 2. Configurar `SANCTUM_STATEFUL_DOMAINS` no `.env`

```dotenv
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost
```

E no `.env.example`:

```dotenv
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost
```

### 3. Garantir que `SESSION_DOMAIN` está correto no `.env`

Para cookie funcionar entre portas diferentes no localhost, o domínio deve ser:

```dotenv
SESSION_DOMAIN=localhost
SESSION_DRIVER=redis
SESSION_SECURE_COOKIE=false   # true apenas em produção (HTTPS)
```

### 4. Corrigir `AuthController::login()` — remover retorno do token no body para SPAs

O token no body é desnecessário para o frontend SPA e expõe a credencial. Manter apenas para documentação/Swagger via `Accept` header:

```php
public function login(Request $request): JsonResponse
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['As credenciais fornecidas estão incorretas.'],
        ]);
    }

    // Inicia sessão via cookie httpOnly (Sanctum SPA)
    auth()->guard('web')->login($user);
    $request->session()->regenerate(); // OWASP: previne session fixation

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ]);
}
```

> **Nota Swagger:** Se quiser manter suporte a Bearer token para o Swagger UI, crie um endpoint separado `/api/auth/token` que gera `createToken()`, exclusivo para clientes não-SPA.

### 5. Corrigir `AuthController::logout()` — invalidar sessão

```php
public function logout(Request $request): JsonResponse
{
    auth()->guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken(); // Regenera CSRF token

    return response()->json(['message' => 'Logout realizado com sucesso.']);
}
```

### 6. Verificar `AuthController::me()` — guard correto

```php
public function me(Request $request): JsonResponse
{
    // auth('sanctum') resolve tanto cookie session quanto Bearer token
    return response()->json(['user' => $request->user()]);
}
```

### 7. Verificar rotas — guard `auth:sanctum` é compatível com ambos os modos

O guard `auth:sanctum` já autentica via cookie de sessão (quando `statefulApi()` está ativo) **e** via Bearer token. Nenhuma mudança necessária no `routes/api.php`.

### 8. Verificar `cors.php` — já está correto

```php
'supports_credentials' => true,  // obrigatório para cookie auth
'allowed_origins' => [env('APP_FRONTEND_URL', 'http://localhost:5173')],
```

> ⚠️ `supports_credentials: true` **nunca** pode ser combinado com `allowed_origins: ['*']` — viola a spec CORS e é rejeitado pelo browser.

### 9. Verificar `api.ts` no frontend — já está correto

```ts
withCredentials: true,  // envia cookies em cross-origin
```

O `initCsrf()` já busca `GET /sanctum/csrf-cookie` antes do login — correto.

### 10. Regenerar cache após todas as alterações

```bash
docker compose exec backend php artisan optimize:clear
docker compose exec backend php artisan config:cache
```

---

## Fluxo completo após a correção

```
1. Frontend → GET /sanctum/csrf-cookie
   └─ Backend define XSRF-TOKEN (cookie legível JS) + laravel_session (httpOnly)

2. Frontend → POST /api/auth/login  { email, password }
   └─ Axios envia X-XSRF-TOKEN header automaticamente (lê do cookie)
   └─ Backend valida CSRF ✓, autentica, regenera session ID
   └─ Response: { user } + cookie laravel_session renovado

3. Frontend → GET /api/auth/me (e demais rotas)
   └─ Browser envia laravel_session automaticamente (httpOnly, sem acesso JS)
   └─ Backend resolve usuário via Sanctum ✓

4. Frontend → POST /api/auth/logout
   └─ Backend invalida sessão + regenera CSRF token
   └─ Cookie expira
```

---

## O que esta abordagem protege (OWASP)

| Ameaça | Proteção |
|---|---|
| **XSS rouba credencial** | Cookie `httpOnly` → inacessível a qualquer script |
| **CSRF** | XSRF-TOKEN validado em toda requisição não-idempotente |
| **Session fixation** | `session()->regenerate()` no login |
| **Token leak no body** | Token não retornado para o SPA |
| **CORS wildcard** | `allowed_origins` restrito ao frontend URL |
