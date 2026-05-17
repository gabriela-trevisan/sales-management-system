# Guia de Melhorias — Auditoria Técnica

**Data da auditoria:** 12 de maio de 2026  
**Versão do projeto:** 0.6.0  
**Escopo:** Backend (Laravel 11), Frontend (React 19 + TypeScript), Infraestrutura (Docker + Nginx)

---

## Índice

1. [✅ Crítico — Autorização por Recurso (IDOR) — Resolvido](#1-✅-crítico--autorização-por-recurso-idor--resolvido)
2. [✅ Crítico — NullPointerException no Repository — Resolvido](#2-✅-crítico--nullpointerexception-no-repository--resolvido)
3. [✅ Crítico — JWT em localStorage (XSS) — Resolvido](#3-✅-crítico--jwt-em-localstorage-xss--resolvido)
4. [✅ Crítico — CORS aberto para qualquer origem — Resolvido](#4-✅-crítico--cors-aberto-para-qualquer-origem--resolvido)
5. [🟡 Importante — Cache key com input não-sanitizado](#5-importante--cache-key-com-input-não-sanitizado)
6. [🟡 Importante — `per_page` sem limite máximo (DoS)](#6-importante--per_page-sem-limite-máximo-dos)
7. [🟡 Importante — `$oldValues` código morto no controller](#7-importante--oldvalues-código-morto-no-controller)
8. [🟡 Importante — `JsonResponse` serializado no cache Redis](#8-importante--jsonresponse-serializado-no-cache-redis)
9. [🟡 Importante — `recentActivities` sem cache](#9-importante--recentactivities-sem-cache)
10. [✅ Importante — Validação de CPF/CNPJ ausente no backend — Resolvido](#10-✅-importante--validação-de-cpfcnpj-ausente-no-backend--resolvido)
11. [🟡 Importante — Conflito de security headers entre Nginx e PHP](#11-importante--conflito-de-security-headers-entre-nginx-e-php)
12. [🟡 Importante — URL da API hardcoded no frontend](#12-importante--url-da-api-hardcoded-no-frontend)
13. [🔵 Melhoria — Assimetria entre use cases no Application layer](#13-melhoria--assimetria-entre-use-cases-no-application-layer)
14. [🔵 Melhoria — `AuthController` fora da camada Presentation](#14-melhoria--authcontroller-fora-da-camada-presentation)
15. [🔵 Melhoria — Envio de email síncrono bloqueia a request](#15-melhoria--envio-de-email-síncrono-bloqueia-a-request)
16. [🔵 Melhoria — `limit` sem cap em `recentActivities`](#16-melhoria--limit-sem-cap-em-recentactivities)
17. [🔵 Melhoria — Eager loading desnecessário em `update()`](#17-melhoria--eager-loading-desnecessário-em-update)
18. [🔵 Melhoria — Parse inseguro de `localStorage` no AuthContext](#18-melhoria--parse-inseguro-de-localstorage-no-authcontext)
19. [🔵 Melhoria — `isRemoteEnabled: true` no DomPDF (SSRF)](#19-melhoria--isremoteenabled-true-no-dompdf-ssrf)
20. [⬜ Ausência de testes automatizados](#20-ausência-de-testes-automatizados)

---

## 1. ✅ Crítico — Autorização por Recurso (IDOR) — Resolvido

### O Problema

**OWASP A01:2021 — Broken Access Control (IDOR — Insecure Direct Object Reference)**

Atualmente, qualquer usuário autenticado pode acessar, modificar ou deletar **qualquer** recurso do sistema, independente de ser o responsável por ele.

```php
// CreateCustomerRequest.php — authorize() retorna sempre true
public function authorize(): bool
{
    return true; // ← qualquer token válido passa
}
```

O fluxo atual:
1. Usuário A cria cliente #100 (ele é o `assigned_to`)
2. Usuário B, autenticado com qualquer token válido, faz `DELETE /api/customers/100`
3. O sistema **deleta** o cliente sem nenhuma verificação de propriedade

Isso viola o princípio de least privilege e é classificado como **Broken Access Control**, a vulnerabilidade #1 do OWASP desde 2021.

### Como Resolver

A solução correta no Laravel são as **Policies** — classes que encapsulam a lógica de autorização por Model, desacoplando-a dos controllers e FormRequests.

#### Passo 1 — Criar a Policy

```php
<?php
// app/Domain/Customer/Policies/CustomerPolicy.php
namespace App\Domain\Customer\Policies;

use App\Domain\Customer\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Qualquer usuário autenticado pode listar clientes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Usuário pode ver um cliente se for o responsável.
     * Adapte para regras de negócio reais (ex: admin vê todos).
     */
    public function view(User $user, Customer $customer): bool
    {
        return $user->id === $customer->assigned_to;
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->id === $customer->assigned_to;
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->id === $customer->assigned_to;
    }
}
```

#### Passo 2 — Registrar a Policy

No Laravel 11, as policies são descobertas automaticamente se o Model e a Policy seguirem a convenção de nomes (`Customer` → `CustomerPolicy`). Verifique se o `AuthServiceProvider` não está interferindo, ou registre explicitamente:

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Gate;
use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Policies\CustomerPolicy;

public function boot(): void
{
    Gate::policy(Customer::class, CustomerPolicy::class);
}
```

#### Passo 3 — Aplicar nos Controllers

```php
// CustomerController.php
public function show(int $id): JsonResponse
{
    $customer = $this->customerRepository->findById($id);

    if (!$customer) {
        return response()->json(['message' => 'Cliente não encontrado'], 404);
    }

    $this->authorize('view', $customer); // ← lança 403 se não autorizado

    return response()->json(['data' => new CustomerResource($customer)]);
}

public function update(UpdateCustomerRequest $request, int $id): JsonResponse
{
    $customer = $this->customerRepository->findById($id);

    if (!$customer) {
        return response()->json(['message' => 'Cliente não encontrado'], 404);
    }

    $this->authorize('update', $customer); // ← 403 se não for o responsável

    // ... resto da lógica
}

public function destroy(int $id): JsonResponse
{
    $customer = $this->customerRepository->findById($id);

    if (!$customer) {
        return response()->json(['message' => 'Cliente não encontrado'], 404);
    }

    $this->authorize('delete', $customer);

    $this->customerRepository->delete($id);
    Cache::forget('dashboard.metrics');

    return response()->json(['message' => 'Cliente removido com sucesso']);
}
```

> **Nota:** O mesmo padrão deve ser aplicado em `ProductController` e `ProposalController`. Para Proposals, a regra de negócio relevante é verificar se o usuário é o `created_by`.

#### Por que não usar `authorize()` no `FormRequest`?

O `FormRequest::authorize()` **não recebe** o Model resolvido — apenas a request bruta. Colocar lógica de IDOR no `FormRequest` requer buscar o model internamente, acoplando a camada de validação à camada de dados. A separação correta é:

- **FormRequest**: valida e sanitiza *dados da entrada*
- **Policy**: verifica *permissão sobre o recurso*
- **Controller**: orquestra a chamada de ambos

> **✅ Resolvido em 15/05/2026** — `CustomerPolicy` (`app/Domain/Customer/Policies/`) e `ProposalPolicy` (`app/Domain/Proposal/Policies/`) criadas com as regras de propriedade (`assigned_to` / `created_by`). Ambas registradas via `Gate::policy()` em `AppServiceProvider::boot()`. `CustomerController` e `ProposalController` chamam `$this->authorize('view'|'update'|'delete', $resource)` antes de qualquer mutação — lança HTTP 403 automaticamente se o usuário não for o responsável.

---

## 2. ✅ Crítico — NullPointerException no Repository — Resolvido

### O Problema

`EloquentCustomerRepository::update()` e `delete()` chamam `findById()`, que retorna `?Customer`. Se o ID não existir, `$customer` é `null` e `$customer->update()` lança um `TypeError` fatal, resultando em HTTP 500 — quando o correto seria HTTP 404.

```php
// EloquentCustomerRepository.php — ATUAL
public function update(int $id, array $data): Customer
{
    $customer = $this->findById($id); // ← retorna ?Customer
    $customer->update($data);         // ← TypeError se $customer for null
    return $customer->fresh();
}
```

Além disso, o controller já busca o cliente *antes* de chamar o repository:

```php
// CustomerController::update — ATUAL
$oldCustomer = $this->customerRepository->findById($id); // busca #1
// ... sem verificação de null aqui antes do update
$customer = $this->customerRepository->update($id, $data); // busca #2 interna
```

Isso resulta em **duas queries SQL** para a mesma linha, com tratamento de null inconsistente.

### Como Resolver

Use `findOrFail()` no Eloquent, que lança `ModelNotFoundException`. O handler global em `bootstrap/app.php` já trata essa exceção e retorna HTTP 404 automaticamente.

```php
// EloquentCustomerRepository.php — CORRETO
public function update(int $id, array $data): Customer
{
    // findOrFail lança ModelNotFoundException → 404 automático pelo handler global
    $customer = Customer::findOrFail($id);
    $customer->update($data);

    // Recarrega com relações para retornar o objeto completo
    return $this->findById($id);
}

public function delete(int $id): bool
{
    return Customer::findOrFail($id)->delete();
}
```

E no controller, remova a busca redundante:

```php
// CustomerController::update — CORRETO
public function update(UpdateCustomerRequest $request, int $id): JsonResponse
{
    // O repository agora lança 404 se não encontrar — sem busca duplicada
    $customer = $this->customerRepository->findById($id);

    if (!$customer) {
        return response()->json(['message' => 'Cliente não encontrado'], 404);
    }

    $this->authorize('update', $customer);

    $data = $request->validated();
    unset($data['assigned_to']);

    $customer = $this->customerRepository->update($id, $data);
    Cache::forget('dashboard.metrics');

    return response()->json([
        'message' => 'Cliente atualizado com sucesso',
        'data' => new CustomerResource($customer),
    ]);
}
```

> O mesmo padrão foi aplicado em `EloquentProductRepository` e `EloquentProposalRepository`.

> **✅ Resolvido em 15/05/2026** — `EloquentCustomerRepository`, `EloquentProductRepository` e `EloquentProposalRepository` usam `findOrFail()` em `update()` e `delete()`. `ModelNotFoundException` é capturada pelo handler global em `bootstrap/app.php` e traduzida para HTTP 404 automaticamente.

---

## 3. ✅ Crítico — JWT em `localStorage` (XSS) — Resolvido

### O Problema

O token Sanctum está sendo salvo em `localStorage`:

```ts
// AuthContext.tsx e api.ts
localStorage.setItem('token', response.token);
const token = localStorage.getItem('token');
```

`localStorage` é acessível por **qualquer JavaScript** executado na página. Um ataque XSS — que pode partir de uma dependência npm comprometida (supply chain attack), um comentário injetado, ou um `dangerouslySetInnerHTML` acidental — expõe o token imediatamente.

### Como Resolver: Sanctum SPA Cookie Authentication

A solução correta é usar cookies `httpOnly; SameSite=Strict`, que são inacessíveis ao JavaScript por definição. O Laravel Sanctum suporta esse modo nativamente para SPAs.

#### Passo 1 — Backend: configurar domínios stateful

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,localhost:5173')),
```

```bash
# backend/.env
SANCTUM_STATEFUL_DOMAINS=localhost:5173,yourdomain.com
SESSION_DOMAIN=localhost
```

#### Passo 2 — Backend: habilitar rota CSRF

```php
// routes/api.php — adicionar antes das rotas protegidas
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});
```

#### Passo 3 — Frontend: axios com cookies

```ts
// services/api.ts
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // ← envia e recebe cookies httpOnly
});

// Antes do login: obter CSRF token
export async function initCsrf(): Promise<void> {
  await api.get('/sanctum/csrf-cookie');
}
```

#### Passo 4 — Frontend: remover localStorage para token

```ts
// authService.ts
export const authService = {
  async login(credentials: LoginCredentials): Promise<User> {
    await initCsrf(); // garante cookie XSRF-TOKEN
    const response = await api.post('/auth/login', credentials);
    return response.data.user; // token não precisa mais ser salvo manualmente
  },

  async logout(): Promise<void> {
    await api.post('/auth/logout');
  },
};
```

```tsx
// AuthContext.tsx — simplificado sem localStorage de token
const [user, setUser] = useState<User | null>(null);

useEffect(() => {
  // Verifica sessão ativa chamando /auth/me
  authService.me()
    .then(setUser)
    .catch(() => setUser(null))
    .finally(() => setIsLoading(false));
}, []);
```

> **Trade-off:** O modo cookie requer que frontend e backend estejam no mesmo domínio ou em domínios configurados como stateful. Para SPA em subdomínio diferente (ex: `app.example.com` → `api.example.com`), configure `SESSION_DOMAIN=.example.com`. Se a arquitetura não permitir essa migração no curto prazo, ao menos limite o impacto com CSP rigorosa e `Subresource Integrity (SRI)` nas dependências externas.

> **✅ Resolvido em 15/05/2026** — `services/api.ts` configurado com `withCredentials: true` e `initCsrf()` obtendo o `XSRF-TOKEN` via `GET /sanctum/csrf-cookie` antes do login. `authService.ts` não persiste nenhum token — a sessão trafega exclusivamente por cookie `httpOnly`. `AuthContext.tsx` armazena apenas metadados do usuário em `localStorage` (nunca o token). `AuthController::login()` inicia sessão SPA com `auth()->guard('web')->login()` + `session()->regenerate()` (previne session fixation), mantendo token Sanctum apenas para clientes não-SPA (Swagger, mobile).

---

## 4. ✅ Crítico — CORS aberto para qualquer origem — Resolvido

### O Problema

```php
// config/cors.php — ATUAL
'allowed_origins' => ['*'],
```

Com `*`, qualquer site da internet pode fazer requisições cross-origin à sua API e receber dados dos usuários autenticados. O comentário no arquivo indica que a restrição estava planejada mas nunca implementada.

### Como Resolver

```php
// config/cors.php — CORRETO
return [
    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        env('APP_FRONTEND_URL', 'http://localhost:5173'),
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Accept', 'Authorization', 'X-Requested-With'],

    'exposed_headers' => [],

    'max_age' => 86400, // 24h de preflight cache

    'supports_credentials' => true, // necessário para cookies httpOnly (item 3)
];
```

```bash
# backend/.env
APP_FRONTEND_URL=http://localhost:5173

# backend/.env.production
APP_FRONTEND_URL=https://app.suaempresa.com.br
```

> Nunca use `['*']` com `supports_credentials: true` — o navegador rejeita essa combinação por spec e é um erro de segurança grave.

> **✅ Resolvido em 15/05/2026** — `config/cors.php` restringe `allowed_origins` a `[env('APP_FRONTEND_URL', 'http://localhost:5173')]`. Inclui `sanctum/csrf-cookie` em `paths`, header `X-XSRF-TOKEN` em `allowed_headers`, `max_age: 86400` e `supports_credentials: true`. Configurar `APP_FRONTEND_URL` no `.env` de cada ambiente garante que produção nunca use a origem padrão de desenvolvimento.

---

## 5. 🟡 Importante — Cache key com input não-sanitizado

### O Problema

```php
// DashboardController.php — ATUAL
$month = $request->query('month'); // pode ser qualquer string
$year  = $request->query('year');
$cacheKey = "dashboard.metrics.{$month}.{$year}"; // injeção de string na chave
```

Problemas:
1. Quando ambos são `null`, a chave vira `dashboard.metrics..` — colisão com outros filtros que resultem em string vazia
2. Input malicioso como `month=all&year=all` colide com a chave de "sem filtro"
3. Caracteres especiais podem causar comportamento inesperado em alguns drivers de cache

### Como Resolver

```php
// DashboardController.php — CORRETO
public function metrics(Request $request): JsonResponse
{
    // Valida e sanitiza parâmetros antes de usá-los
    $month = $request->query('month');
    $year  = $request->query('year');

    if ($month !== null && !preg_match('/^\d{2}$/', $month)) {
        return response()->json(['message' => 'Parâmetro month inválido. Use formato MM (ex: 01).'], 422);
    }
    if ($year !== null && !preg_match('/^\d{4}$/', $year)) {
        return response()->json(['message' => 'Parâmetro year inválido. Use formato YYYY (ex: 2026).'], 422);
    }

    // Chave explícita e sem ambiguidade
    $cacheKey = 'dashboard.metrics:' . ($month ?? 'all') . ':' . ($year ?? 'all');

    $data = Cache::remember($cacheKey, 300, fn() => $this->calculateMetrics($month, $year));

    return response()->json($data);
}
```

> Use `:` como separador em vez de `.` para compatibilidade universal com drivers de cache (Redis, Memcached, database).

---

## 6. 🟡 Importante — `per_page` sem limite máximo (DoS)

### O Problema

```php
// CustomerController.php — ATUAL
$customers = $this->customerRepository->getAll(
    array_filter($filters),
    $request->get('per_page', 15) // sem limite máximo
);
```

Um cliente enviando `?per_page=999999` força uma query que retorna toda a tabela de uma vez — memória excessiva, timeout e potencial negação de serviço.

### Como Resolver

Aplique o cap em todos os controllers que aceitam `per_page`:

```php
// CustomerController.php, ProductController.php, ProposalController.php
$perPage = min(max(1, (int) $request->get('per_page', 15)), 100);
```

Para evitar repetição, extraia um método helper protegido na classe base `Controller`:

```php
// app/Http/Controllers/Controller.php
protected function getPerPage(Request $request, int $default = 15, int $max = 100): int
{
    return min(max(1, (int) $request->get('per_page', $default)), $max);
}
```

```php
// Uso em qualquer controller
$perPage = $this->getPerPage($request);
```

---

## 7. 🟡 Importante — `$oldValues` código morto no controller

### O Problema

```php
// CustomerController::update — ATUAL
$oldCustomer = $this->customerRepository->findById($id);
$oldValues = [          // ← capturado mas nunca usado
    'name' => $oldCustomer->name,
    'email' => $oldCustomer->email,
    'phone' => $oldCustomer->phone,
    'status' => $oldCustomer->status,
];

$data = $request->validated();
unset($data['assigned_to']);

$customer = $this->customerRepository->update($id, $data); // auditing acontece aqui automaticamente
```

O `laravel-auditing` já captura os valores anteriores (`old_values`) e novos (`new_values`) automaticamente via `$auditInclude`. O código manual é redundante, enganoso (parece que algo será feito com `$oldValues`) e causa uma query extra desnecessária.

### Como Resolver

Remova as linhas de captura manual:

```php
// CustomerController::update — CORRETO
public function update(UpdateCustomerRequest $request, int $id): JsonResponse
{
    $customer = $this->customerRepository->findById($id);

    if (!$customer) {
        return response()->json(['message' => 'Cliente não encontrado'], 404);
    }

    $this->authorize('update', $customer);

    $data = $request->validated();
    unset($data['assigned_to']);

    $customer = $this->customerRepository->update($id, $data);
    Cache::forget('dashboard.metrics');

    return response()->json([
        'message' => 'Cliente atualizado com sucesso',
        'data' => new CustomerResource($customer),
    ]);
}
```

> O `laravel-auditing` registra `old_values` e `new_values` automaticamente no evento `updated` do Eloquent — não é necessário nenhum código manual.

> **✅ Resolvido em 15/05/2026** — `CustomerController::update()` não captura mais `$oldValues` manualmente. O `laravel-auditing` registra os valores anteriores e novos automaticamente via `$auditInclude` no evento `updated` do Eloquent — sem queries extras.

---

## 8. 🟡 Importante — `JsonResponse` serializado no cache Redis

### O Problema

```php
// DashboardController::metrics — ATUAL
return Cache::remember($cacheKey, 300, function () use ($month, $year) {
    return $this->calculateMetrics($month, $year); // retorna JsonResponse ← ERRADO
});
```

`Cache::remember()` armazena o **valor de retorno da closure** no Redis via serialização PHP. Um objeto `JsonResponse` contém streams, headers e referências internas que não serializam de forma limpa — o resultado pode ser um objeto corrompido no cache ou um cache miss silencioso a cada request.

O cache deve armazenar **dados primitivos** (arrays, strings, números), não objetos de resposta HTTP.

### Como Resolver

```php
// DashboardController.php — CORRETO

// calculateMetrics retorna array, não JsonResponse
private function calculateMetrics(?string $month, ?string $year): array
{
    // ... toda a lógica existente ...

    // Trocar o return final de:
    return response()->json([...]);

    // Para:
    return [
        'total_customers' => $totalCustomers,
        'total_customers_previous' => $totalCustomersPrevious,
        'total_customers_trend' => $customersTrend,
        'total_opportunities' => $totalOpportunities,
        'total_opportunities_previous' => $totalOpportunitiesPrevious,
        'total_opportunities_trend' => $opportunitiesTrend,
        'total_pipeline_value' => (float) $totalPipelineValue,
        'conversion_rate' => $conversionRate,
        'monthly_sales' => $monthlySales,
        'opportunities_by_stage' => $opportunitiesByStage,
    ];
}

// No método metrics():
public function metrics(Request $request): JsonResponse
{
    // ... validação e sanitização de $month e $year ...

    $cacheKey = 'dashboard.metrics:' . ($month ?? 'all') . ':' . ($year ?? 'all');

    $data = Cache::remember($cacheKey, 300, fn() => $this->calculateMetrics($month, $year));

    return response()->json($data); // JsonResponse criado FORA do cache
}
```

O mesmo padrão vale para `customersBySegment`.

---

## 9. 🟡 Importante — `recentActivities` sem cache

### O Problema

Enquanto `metrics` e `customersBySegment` são cacheados por 5 minutos, `recentActivities` executa duas queries com JOINs em cada request — sem nenhum cache. A inconsistência não tem justificativa documentada.

### Como Resolver

Adicionar cache com TTL curto (dados que mudam com frequência merecem TTL menor):

```php
// DashboardController::recentActivities — CORRETO
public function recentActivities(Request $request): JsonResponse
{
    $limit = min((int) $request->query('limit', 10), 50);
    $month = $request->query('month');
    $year  = $request->query('year');

    // Sanitização
    if ($month !== null && !preg_match('/^\d{2}$/', $month)) {
        return response()->json(['message' => 'Parâmetro month inválido.'], 422);
    }

    $cacheKey = 'dashboard.activities:' . $limit . ':' . ($month ?? 'all') . ':' . ($year ?? 'all');

    $activities = Cache::remember($cacheKey, 60, function () use ($limit, $month, $year) {
        // ... lógica existente, retornando array em vez de JsonResponse ...
        return $this->fetchRecentActivities($limit, $month, $year);
    });

    return response()->json(['activities' => $activities]);
}
```

---

## 10. ✅ Importante — Validação de CPF/CNPJ ausente no backend — Resolvido

### O Problema

O frontend valida os dígitos verificadores de CPF/CNPJ via Zod, mas o backend apenas valida o comprimento (`max:14`). Qualquer chamada direta à API (Postman, scripts, outros clientes) pode inserir documentos inválidos.

**Regra de segurança fundamental:** o backend nunca deve confiar na validação do cliente. Toda validação deve ser repetida server-side.

### Como Resolver

#### Passo 1 — Criar a Rule reutilizável

```php
<?php
// app/Domain/Customer/Rules/ValidDocumentRule.php
namespace App\Domain\Customer\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidDocumentRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $document = preg_replace('/\D/', '', (string) $value);

        if (strlen($document) === 11) {
            if (!$this->isValidCpf($document)) {
                $fail('O CPF informado é inválido.');
            }
            return;
        }

        if (strlen($document) === 14) {
            if (!$this->isValidCnpj($document)) {
                $fail('O CNPJ informado é inválido.');
            }
            return;
        }

        $fail('O documento deve ser um CPF (11 dígitos) ou CNPJ (14 dígitos).');
    }

    private function isValidCpf(string $cpf): bool
    {
        // Rejeita sequências trivialmente inválidas
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $firstDigit = $remainder < 2 ? 0 : 11 - $remainder;

        if ((int) $cpf[9] !== $firstDigit) {
            return false;
        }

        // Validação do segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $secondDigit = $remainder < 2 ? 0 : 11 - $remainder;

        return (int) $cpf[10] === $secondDigit;
    }

    private function isValidCnpj(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Cálculo dos dígitos verificadores do CNPJ
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights1[$i];
        }
        $remainder = $sum % 11;
        $firstDigit = $remainder < 2 ? 0 : 11 - $remainder;

        if ((int) $cnpj[12] !== $firstDigit) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $weights2[$i];
        }
        $remainder = $sum % 11;
        $secondDigit = $remainder < 2 ? 0 : 11 - $remainder;

        return (int) $cnpj[13] === $secondDigit;
    }
}
```

#### Passo 2 — Aplicar nos FormRequests

```php
// CreateCustomerRequest.php e UpdateCustomerRequest.php
use App\Domain\Customer\Rules\ValidDocumentRule;

public function rules(): array
{
    return [
        'document' => ['required', 'string', new ValidDocumentRule(), 'unique:customers,document'],
        // ...
    ];
}
```

> **✅ Resolvido em 19/05/2026** — `Document` Value Object (`app/Domain/Shared/ValueObjects/Document.php`) implementa algoritmo módulo-11 oficial da Receita Federal com `isValidCpf()` e `isValidCnpj()`. Rejeita CPFs/CNPJs com dígitos verificadores inválidos e sequências homogêneas (ex: `111.111.111-11`). Mutator `Customer::setDocumentAttribute()` delega para `Document::fromString()`, que lança `InvalidDomainStateException` em documentos inválidos. 11 testes unitários cobrindo CPF/CNPJ válidos, inválidos, formatados, não formatados, homogêneos e comprimento incorreto — todos passando.

---

## 11. 🟡 Importante — Conflito de security headers entre Nginx e PHP

### O Problema

O middleware `SecurityHeaders.php` define `X-Frame-Options: DENY`, enquanto `docker/nginx/default.conf` define `X-Frame-Options: SAMEORIGIN` no mesmo servidor. Quando ambos estão ativos, o header pode ser **duplicado** na resposta HTTP, causando comportamento indefinido por spec — alguns navegadores usam o primeiro valor, outros rejeitam a resposta.

```nginx
# default.conf — atual
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
```

```php
// SecurityHeaders.php — atual
$response->headers->set('X-Frame-Options', 'DENY'); // valor diferente!
```

### Como Resolver

**Escolha um único ponto de controle.** A recomendação é centralizar no **Nginx** para maior performance (os headers são adicionados antes do PHP processar qualquer coisa) e remover do middleware PHP.

#### Nginx atualizado (única fonte de verdade)

```nginx
# docker/nginx/default.conf
server {
    listen 8000;
    server_name localhost;

    root /var/www/backend/public;
    index index.php;

    # Security Headers — gerenciados apenas aqui
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;
    # X-XSS-Protection é obsoleto em navegadores modernos (Chrome 78+)
    # Não adicione — pode causar comportamentos inesperados em alguns browsers

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass backend:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

E no middleware PHP, mantenha apenas o HSTS (que depende do ambiente) e o CSP (que varia por ambiente e é mais complexo de configurar no Nginx):

```php
// SecurityHeaders.php — simplificado
public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);

    // CSP varia por ambiente — mantém no PHP
    $response->headers->set('Content-Security-Policy', $this->buildCspPolicy());

    // HSTS apenas em produção
    if (app()->environment('production')) {
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );
    }

    return $response;
}
```

---

## 12. 🟡 Importante — URL da API hardcoded no frontend

### O Problema

```ts
// src/services/api.ts — ATUAL
const api = axios.create({
  baseURL: 'http://localhost:8000/api', // ← hardcoded, ignora o .env
```

A variável `VITE_API_URL` está definida no `.env`:
```bash
VITE_API_URL=http://localhost:8000/api
```

...mas nunca é lida. Em qualquer ambiente diferente de desenvolvimento local (staging, produção, CI), o frontend sempre apontará para `localhost`.

### Como Resolver

```ts
// src/services/api.ts — CORRETO
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // necessário se migrar para cookies (item 3)
});
```

Se quiser uma fallback segura com aviso:

```ts
const apiUrl = import.meta.env.VITE_API_URL;

if (!apiUrl && import.meta.env.DEV) {
  console.warn('[api] VITE_API_URL não definida no .env. Usando fallback localhost.');
}

const api = axios.create({
  baseURL: apiUrl ?? 'http://localhost:8000/api',
  // ...
});
```

> **✅ Resolvido em 15/05/2026** — `src/services/api.ts` usa `import.meta.env.VITE_API_URL` como `baseURL`. O `docker-compose.yml` define `VITE_API_URL=/api` (URL relativa encaminhada pelo Vite proxy ao Nginx internamente), eliminando qualquer referência hardcoded a `localhost`.

---

## 13. 🔵 Melhoria — Assimetria entre use cases no Application layer

### O Problema

Apenas `CreateCustomer` passa pelo Application layer (Command + Handler). As operações `update`, `delete` e `show` são executadas diretamente do controller via `$this->customerRepository`. Isso cria uma inconsistência arquitetural:

- **Criar cliente:** `Controller → Command → Handler → Repository`
- **Atualizar cliente:** `Controller → Repository` (sem Handler)

Se a regra de negócio de update crescer (ex: disparar um evento, enviar notificação, registrar log específico), a lógica irá direto para o controller.

### Como Resolver

**Opção A — Completar o Application layer para update/delete (DDD puro):**

```php
// app/Application/Customer/UpdateCustomer/UpdateCustomerCommand.php
class UpdateCustomerCommand
{
    public function __construct(
        public readonly int $customerId,
        public readonly string $name,
        public readonly string $email,
        // ... demais campos sem assigned_to (regra de negócio)
    ) {}
}

// app/Application/Customer/UpdateCustomer/UpdateCustomerHandler.php
class UpdateCustomerHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository
    ) {}

    public function handle(UpdateCustomerCommand $command): Customer
    {
        $data = [
            'name' => $command->name,
            'email' => $command->email,
            // ... assigned_to é excluído aqui, na camada correta
        ];

        return $this->customerRepository->update($command->customerId, $data);
    }
}
```

**Opção B — Simplificar e usar um CustomerService (pragmático):**

Se não há Command Bus planejado, substituir Command + Handler por um Service remove a cerimoniosidade sem perder organização:

```php
// app/Application/Customer/CustomerService.php
class CustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository
    ) {}

    public function create(array $data): Customer
    {
        if ($this->customerRepository->findByDocument($data['document'])) {
            throw ValidationException::withMessages(['document' => ['Documento já cadastrado.']]);
        }
        return $this->customerRepository->create($data);
    }

    public function update(int $id, array $data): Customer
    {
        unset($data['assigned_to']); // regra de negócio centralizada
        return $this->customerRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->customerRepository->delete($id);
    }
}
```

> **Recomendação:** Para este projeto, a Opção B é mais coerente com o tamanho do domínio. O Command Pattern agrega valor real quando há um Command Bus que permite decoradores (logging, retry, transactions) ou múltiplos handlers — sem bus, é cerimoniosidade sem benefício prático.

---

## 14. 🔵 Melhoria — `AuthController` fora da camada Presentation

### O Problema

```
app/Http/Controllers/API/Auth/AuthController.php       ← atual
app/Presentation/Http/Controllers/API/...              ← onde deveria estar
```

Todo o resto do sistema usa `Presentation/Http/Controllers/API/*`, mas o `AuthController` está em `Http/Controllers/API/Auth/`. Isso quebra a consistência arquitetural e confunde quem navega pelo projeto.

### Como Resolver

Mover o arquivo e atualizar o namespace:

```
app/Presentation/Http/Controllers/API/Auth/AuthController.php
```

```php
// Namespace atualizado
namespace App\Presentation\Http\Controllers\API\Auth;
```

```php
// routes/api.php — atualizar import
use App\Presentation\Http\Controllers\API\Auth\AuthController;
```

---

## 15. 🔵 Melhoria — Envio de email síncrono bloqueia a request

### O Problema

```php
// ProposalController::sendEmail
public function sendEmail(Request $request, int $id)
{
    // ...
    $success = $this->emailService->send($proposal); // síncrono: gera PDF + envia email
    // ...
}
```

`SendProposalEmailService::send()` gera o PDF com DomPDF e envia o email de forma síncrona durante a request. O DomPDF pode levar de 1 a 5 segundos para renderizar propostas complexas — o cliente fica aguardando todo esse tempo com a conexão aberta.

### Como Resolver

Criar um Job assíncrono:

```php
<?php
// app/Application/Proposal/SendProposalEmail/SendProposalEmailJob.php
namespace App\Application\Proposal\SendProposalEmail;

use App\Domain\Proposal\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendProposalEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public array $backoff = [10, 30, 60]; // segundos entre retries

    public function __construct(
        private readonly Proposal $proposal,
        private readonly ?string $emailTo = null,
    ) {}

    public function handle(SendProposalEmailService $emailService): void
    {
        $emailService->send($this->proposal, $this->emailTo);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Falha ao enviar email de proposta', [
            'proposal_id' => $this->proposal->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

```php
// ProposalController::sendEmail — CORRETO
public function sendEmail(Request $request, int $id): JsonResponse
{
    $proposal = $this->proposalRepository->findById($id);

    if (!$proposal) {
        return response()->json(['message' => 'Proposta não encontrada.'], 404);
    }

    $emailTo = $request->input('email_to');

    SendProposalEmailJob::dispatch($proposal, $emailTo);

    return response()->json([
        'message' => 'Email sendo processado. O cliente receberá em breve.',
    ]);
}
```

> O `QUEUE_CONNECTION=database` já está configurado no `.env` — sem necessidade de Redis para filas neste momento.

---

## 16. 🔵 Melhoria — `limit` sem cap em `recentActivities`

### O Problema

```php
$limit = $request->query('limit', 10); // sem validação
// ...
->limit((int) ceil($limit / 2)) // pode ser limit(500000)
```

### Como Resolver

```php
$limit = min(max(1, (int) $request->query('limit', 10)), 50);
```

> **✅ Resolvido em 15/05/2026** — `DashboardController::recentActivities()` usa `min(max((int) $request->query('limit', '10'), 1), 100)`, capando o limite em 100 registros.

---

## 17. 🔵 Melhoria — Eager loading desnecessário em `update()`

### O Problema

```php
// EloquentCustomerRepository::update — ATUAL
public function update(int $id, array $data): Customer
{
    $customer = $this->findById($id); // ← carrega addresses, contacts, assignedUser, segment
    $customer->update($data);
    return $customer->fresh(); // ← recarrega sem as relações
}
```

`findById()` executa 5 queries (o model + 4 eager loads) só para obter uma instância do Model e chamar `update()`. O `.fresh()` recarrega sem as relações, então as 4 queries de eager load foram completamente desnecessárias.

### Como Resolver

```php
// EloquentCustomerRepository::update — CORRETO
public function update(int $id, array $data): Customer
{
    $customer = Customer::findOrFail($id); // 1 query apenas
    $customer->update($data);
    return $this->findById($id); // recarrega completo com relações para o retorno da API
}
```

> **✅ Resolvido em 15/05/2026** — `EloquentCustomerRepository::update()` usa `Customer::findOrFail($id)` (1 query) em vez de `findById()` (5 queries com eager load de relações). O `findById()` completo é chamado apenas no `return`, garantindo que o response da API contenha os dados com todas as relações.

---

## 18. 🔵 Melhoria — Parse inseguro de `localStorage` no AuthContext

### O Problema

```ts
// AuthContext.tsx — ATUAL
const storedUser = localStorage.getItem('user');
if (storedToken && storedUser) {
    setUser(JSON.parse(storedUser)); // ← sem validação de shape
}
```

Se o `localStorage` contiver um objeto malformado (manipulação manual, dado de versão anterior, ou injeção via XSS), `JSON.parse()` pode retornar um objeto que não corresponde ao tipo `User`, causando crashes em runtime ou, pior, autenticação indevida.

### Como Resolver

Usar Zod para validar o shape do objeto:

```ts
// src/types/auth.ts
import { z } from 'zod';

export const userSchema = z.object({
    id: z.number(),
    name: z.string(),
    email: z.string().email(),
    email_verified_at: z.string().nullable().optional(),
    created_at: z.string().optional(),
});

export type User = z.infer<typeof userSchema>;
```

```ts
// AuthContext.tsx — CORRETO
import { userSchema } from '@/types/auth';

useEffect(() => {
    const storedToken = localStorage.getItem('token');
    const storedUser = localStorage.getItem('user');

    if (storedToken && storedUser) {
        try {
            const parsed = userSchema.safeParse(JSON.parse(storedUser));
            if (parsed.success) {
                setToken(storedToken);
                setUser(parsed.data);
            } else {
                // Dado inválido no localStorage — limpa tudo
                localStorage.removeItem('token');
                localStorage.removeItem('user');
            }
        } catch {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
        }
    }
    setIsLoading(false);
}, []);
```

---

## 19. 🔵 Melhoria — `isRemoteEnabled: true` no DomPDF (SSRF)

### O Problema

```php
// GenerateProposalPdfService.php
$pdf->setOption('isRemoteEnabled', true); // permite fetch de URLs externas
```

Com `isRemoteEnabled: true`, se qualquer campo do template Blade (notes, nome do cliente, etc.) contiver uma URL e esta chegar ao template, o DomPDF fará uma requisição HTTP para essa URL durante a geração do PDF — isso é um **Server-Side Request Forgery (SSRF)**. Um atacante poderia fazer o servidor fazer requisições para serviços internos (metadados AWS, Redis, bancos locais).

### Como Resolver

```php
// GenerateProposalPdfService.php — CORRETO
$pdf->setOption('isRemoteEnabled', false);
```

Se o template precisa de imagens (ex: logo da empresa), use **base64 inline** em vez de URLs externas:

```blade
{{-- resources/views/proposals/pdf.blade.php --}}
@php
    $logoPath = public_path('images/logo.png');
    $logoBase64 = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/png;base64,' . $logoBase64;
@endphp

<img src="{{ $logoSrc }}" alt="Logo">
```

---

## 20. ⬜ Ausência de Testes Automatizados

### O Problema

Os únicos arquivos de teste são:
```
tests/Feature/ExampleTest.php  ← placeholder gerado pelo Laravel
tests/Unit/ExampleTest.php     ← placeholder gerado pelo Laravel
```

Zero cobertura funcional. Em um projeto com lógica de negócio real (cálculo de comissões, geração de PDF, pipeline de vendas, auditoria LGPD), a ausência de testes significa que qualquer refatoração é feita às cegas.

### Como Resolver

Priorize testes nas camadas de maior risco:

#### Testes de Feature (integração HTTP)

```php
// tests/Feature/API/Auth/LoginTest.php
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertUnprocessable();
    }

    public function test_login_is_rate_limited_after_5_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', ['email' => 'x@x.com', 'password' => 'wrong']);
        }

        $this->postJson('/api/auth/login', ['email' => 'x@x.com', 'password' => 'wrong'])
            ->assertStatus(429);
    }
}
```

```php
// tests/Feature/API/Customer/CustomerAuthorizationTest.php
class CustomerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_delete_customer_belonging_to_another_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $customer = Customer::factory()->create(['assigned_to' => $owner->id]);

        $this->actingAs($other)
            ->deleteJson("/api/customers/{$customer->id}")
            ->assertForbidden(); // 403
    }

    public function test_user_can_delete_own_customer(): void
    {
        $owner = User::factory()->create();
        $customer = Customer::factory()->create(['assigned_to' => $owner->id]);

        $this->actingAs($owner)
            ->deleteJson("/api/customers/{$customer->id}")
            ->assertOk();
    }
}
```

#### Testes de Unit (regras de negócio)

```php
// tests/Unit/Rules/ValidDocumentRuleTest.php
class ValidDocumentRuleTest extends TestCase
{
    public function test_valid_cpf_passes(): void
    {
        $rule = new ValidDocumentRule();
        $failed = false;
        $rule->validate('document', '529.982.247-25', function() use (&$failed) { $failed = true; });
        $this->assertFalse($failed);
    }

    public function test_invalid_cpf_fails(): void
    {
        $rule = new ValidDocumentRule();
        $failed = false;
        $rule->validate('document', '111.111.111-11', function() use (&$failed) { $failed = true; });
        $this->assertTrue($failed);
    }

    public function test_valid_cnpj_passes(): void
    {
        $rule = new ValidDocumentRule();
        $failed = false;
        $rule->validate('document', '11.222.333/0001-81', function() use (&$failed) { $failed = true; });
        $this->assertFalse($failed);
    }
}
```

#### Ordem de prioridade para implementação

| Prioridade | Teste | Justificativa |
|---|---|---|
| 1 | `CustomerAuthorizationTest` | Valida o BLOCKER-1 (IDOR) |
| 2 | `LoginTest` | Autenticação é a porta de entrada |
| 3 | `ValidDocumentRuleTest` | Regra de negócio com algoritmo específico |
| 4 | `CustomerCrudTest` | Fluxo principal da aplicação |
| 5 | `ProposalCrudTest` | Segundo fluxo principal |
| 6 | `DashboardMetricsTest` | Cache e cálculo de métricas |

---

## Resumo de Prioridades

| # | Problema | Severidade | Status |
|---|---|---|---|
| 1 | Autorização por recurso (IDOR) | 🔴 Crítico | ✅ Resolvido |
| 2 | NullPointerException no repository | 🔴 Crítico | ✅ Resolvido |
| 3 | JWT em localStorage (XSS) | 🔴 Crítico | ✅ Resolvido |
| 4 | CORS aberto | 🔴 Crítico | ✅ Resolvido |
| 5 | Cache key com input não-sanitizado | 🟡 Importante | ⬛ Pendente |
| 6 | `per_page` sem limite (DoS) | 🟡 Importante | ⬛ Pendente |
| 7 | `$oldValues` código morto | 🟡 Importante | ✅ Resolvido |
| 8 | `JsonResponse` no cache Redis | 🟡 Importante | ⬛ Pendente |
| 9 | `recentActivities` sem cache | 🟡 Importante | ⬛ Pendente |
| 10 | Validação CPF/CNPJ ausente no backend | 🟡 Importante | ⬛ Pendente |
| 11 | Conflito de headers Nginx/PHP | 🟡 Importante | ⬛ Pendente |
| 12 | URL da API hardcoded | 🟡 Importante | ✅ Resolvido |
| 13 | Assimetria no Application layer | 🔵 Melhoria | ⬛ Pendente |
| 14 | `AuthController` fora da camada Presentation | 🔵 Melhoria | ⬛ Pendente |
| 15 | Email síncrono bloqueia request | 🔵 Melhoria | ⬛ Pendente |
| 16 | `limit` sem cap em activities | 🔵 Melhoria | ✅ Resolvido |
| 17 | Eager loading excessivo em update | 🔵 Melhoria | ✅ Resolvido |
| 18 | Parse inseguro de localStorage | 🔵 Melhoria | ⬛ Pendente |
| 19 | `isRemoteEnabled` no DomPDF (SSRF) | 🔵 Melhoria | ⬛ Pendente |
| 20 | Ausência de testes | ⬜ Estrutural | ⬛ Pendente |

---

*Documento gerado com base na auditoria técnica de 12/05/2026.*
