# Badges do Repositório — Plano e Acompanhamento

**Objetivo:** Tornar o repositório visualmente profissional no GitHub com badges dinâmicos que demonstram qualidade de código, cobertura de testes e segurança.

> **Referência interna:** [PORTFOLIO_PROJECTS_PLAN.md](../../PORTFOLIO_PROJECTS_PLAN.md) · [CODE_QUALITY.md](CODE_QUALITY.md)

---

## 📊 Status Atual

| Badge | Tipo | Status | Prioridade |
|---|---|---|---|
| CI passing (GitHub Actions) | Dinâmico | ⏳ Fase 1 | 🔴 Alta |
| PHPStan Level 6 | Estático | ✅ Pode adicionar agora | 🟡 Média |
| Cobertura de testes (Codecov) | Dinâmico | ⏳ Fase 2 | 🔴 Alta |
| Segurança deps (Snyk) | Dinâmico | ⏳ Fase 3 | 🟡 Média |
| Qualidade (SonarCloud) | Dinâmico | ⏳ Fase 3 | 🟢 Baixa |
| Dependabot ativo | Nativo GitHub | ⏳ Fase 3 | 🟡 Média |
| Licença MIT | Estático | ✅ Pode adicionar agora | 🟢 Baixa |

---

## 🟢 Fase 1 — Badge Verde de CI (Quick Win)

**Objetivo:** Ter o pipeline de CI rodando e passando no GitHub Actions.  
**Estimativa:** 1 dia de trabalho.  
**Impacto:** Badge verde visível logo abaixo do título no README. Sinaliza ao recrutador que "o código roda e os testes passam".

### Checklist

- [x] Criar `.github/workflows/ci.yml` com jobs backend e frontend
- [x] Descomentar SQLite in-memory no `phpunit.xml` (para suporte a feature tests futuros)
- [ ] Fazer push para o GitHub e verificar que o workflow executa com sucesso
- [ ] Adicionar badge ao `README.md` após confirmação verde

### Workflow criado: `.github/workflows/ci.yml`

**Job: backend**
- PHP 8.3 via `shivammathur/setup-php`
- Cache de `vendor/` por `composer.lock`
- `composer install`
- Copia `.env.example` → `.env` + `php artisan key:generate`
- `php artisan test --testsuite=Unit` (5 testes de domínio passando)
- `vendor/bin/phpstan analyse` (Level 6)

**Job: frontend**
- Node.js 22 via `actions/setup-node`
- Cache de `node_modules/` por `package-lock.json`
- `npm ci`
- `npx tsc --noEmit` (TypeScript type check)
- `npm run lint` (ESLint)
- `npm run build` (Vite build — valida que nenhum erro de compilação existe)

### Badge a adicionar no README após o push

```markdown
[![CI](https://github.com/gabriela-trevisan/sales-management-system/actions/workflows/ci.yml/badge.svg)](https://github.com/gabriela-trevisan/sales-management-system/actions)
```


### Observações técnicas

- Os 5 testes unitários existentes (`DocumentTest`, `MoneyTest`, `ProposalLineAmountTest`, `ProposalStatusTest`, `ProposalAggregateTotalsTest`) estendem `PHPUnit\Framework\TestCase` diretamente — **não precisam de banco de dados**.
- O `Feature/ExampleTest.php` (placeholder do Laravel) faz `$this->get('/')` e espera HTTP 200 — **excluído do job de CI** pois não há rota web configurada. Deve ser removido ou reescrito quando os Feature tests forem implementados.
- O workflow executa apenas `--testsuite=Unit` intencionalmente até que os Feature tests sejam escritos com banco SQLite (Fase 2).

---

## 🔵 Fase 2 — Badge de Cobertura (Codecov)

**Objetivo:** Exibir o percentual de cobertura de testes no README.  
**Estimativa:** 1–2 semanas (inclui escrita dos Feature tests).  
**Meta de cobertura:** 70%+ (backend).

### Checklist

- [ ] Criar conta gratuita em [codecov.io](https://codecov.io) e conectar o repositório
- [ ] Adicionar `CODECOV_TOKEN` como secret no repositório GitHub
- [ ] Alterar `coverage: none` → `coverage: xdebug` no workflow `ci.yml`
- [ ] Adicionar step de geração de relatório (`--coverage-clover=coverage.xml`)
- [ ] Adicionar step de upload para Codecov (`codecov/codecov-action@v4`)
- [ ] Escrever Feature tests prioritários (ver lista abaixo)
- [ ] Adicionar badge ao `README.md`

### Feature tests a escrever (por prioridade)

| Arquivo | Testa | Cobertura esperada |
|---|---|---|
| `tests/Feature/API/Auth/LoginTest.php` | Login, logout, me, rate limit | Alta |
| `tests/Feature/API/Customer/CustomerCRUDTest.php` | CRUD completo de clientes | Alta |
| `tests/Feature/API/Customer/CustomerAuthorizationTest.php` | Política de acesso (403) | Alta |
| `tests/Feature/API/Product/ProductCRUDTest.php` | CRUD completo de produtos | Média |
| `tests/Feature/API/Proposal/ProposalCRUDTest.php` | Criação e edição de propostas | Média |

### Badge a adicionar no README

```markdown
[![Coverage](https://codecov.io/gh/gabriela-trevisan/sales-management-system/branch/main/graph/badge.svg)](https://codecov.io/gh/gabriela-trevisan/sales-management-system)
```

### Alteração necessária no ci.yml (step backend)

```yaml
- name: Setup PHP 8.3
  uses: shivammathur/setup-php@v2
  with:
    php-version: '8.3'
    extensions: mbstring, pdo, pdo_sqlite, sqlite3
    coverage: xdebug          # ← alterar de 'none' para 'xdebug'

- name: Run PHPUnit with coverage
  working-directory: backend
  run: php artisan test --testsuite=Unit --coverage-clover=coverage.xml

- name: Upload coverage to Codecov
  uses: codecov/codecov-action@v4
  with:
    files: backend/coverage.xml
    token: ${{ secrets.CODECOV_TOKEN }}
```

---

## 🟣 Fase 3 — Segurança + Qualidade + Dependabot

**Objetivo:** Demonstrar maturidade em segurança e manutenção contínua.  
**Estimativa:** 2–4 semanas.

### 3a. Snyk — Vulnerabilidades em Dependências

**Checklist:**
- [ ] Criar conta gratuita em [snyk.io](https://snyk.io) e conectar o repositório
- [ ] Adicionar `SNYK_TOKEN` como secret no GitHub
- [ ] Adicionar job `snyk` ao `ci.yml`:

```yaml
snyk:
  name: Security — Snyk Dependency Scan
  runs-on: ubuntu-latest
  steps:
    - uses: actions/checkout@v4
    - uses: snyk/actions/php@master
      with:
        args: --file=backend/composer.json
      env:
        SNYK_TOKEN: ${{ secrets.SNYK_TOKEN }}
```

- [ ] Adicionar badge ao `README.md`:

```markdown
[![Known Vulnerabilities](https://snyk.io/test/github/gabriela-trevisan/sales-management-system/badge.svg)](https://snyk.io/test/github/gabriela-trevisan/sales-management-system)
```

### 3b. SonarCloud — Qualidade de Código

**Checklist:**
- [ ] Criar conta gratuita em [sonarcloud.io](https://sonarcloud.io) e importar o repositório
- [ ] Criar arquivo `sonar-project.properties` na raiz
- [ ] Adicionar `SONAR_TOKEN` como secret no GitHub
- [ ] Adicionar step ao `ci.yml`
- [ ] Adicionar badge ao `README.md`

**`sonar-project.properties`:**
```properties
sonar.projectKey=gabriela-trevisan_sales-management-system
sonar.organization=gabriela-trevisan
sonar.sources=backend/app,frontend/src
sonar.exclusions=backend/vendor/**,frontend/node_modules/**
sonar.php.coverage.reportPaths=backend/coverage.xml
```

### 3c. Dependabot — Updates Automáticos

**Checklist:**
- [ ] Criar `.github/dependabot.yml`
- [ ] Verificar primeiro PR criado pelo Dependabot

**`.github/dependabot.yml`:**
```yaml
version: 2
updates:
  - package-ecosystem: "composer"
    directory: "/backend"
    schedule:
      interval: "weekly"
    labels: ["dependencies", "php"]

  - package-ecosystem: "npm"
    directory: "/frontend"
    schedule:
      interval: "weekly"
    labels: ["dependencies", "javascript"]

  - package-ecosystem: "github-actions"
    directory: "/"
    schedule:
      interval: "monthly"
    labels: ["dependencies", "ci"]
```

---

## 📌 README.md — Bloco de Badges Final

Após completar todas as fases, o bloco de badges no `README.md` ficará:

```markdown
[![CI](https://github.com/gabriela-trevisan/sales-management-system/actions/workflows/ci.yml/badge.svg)](https://github.com/gabriela-trevisan/sales-management-system/actions)
[![Coverage](https://codecov.io/gh/gabriela-trevisan/sales-management-system/branch/main/graph/badge.svg)](https://codecov.io/gh/gabriela-trevisan/sales-management-system)
[![Known Vulnerabilities](https://snyk.io/test/github/gabriela-trevisan/sales-management-system/badge.svg)](https://snyk.io/test/github/gabriela-trevisan/sales-management-system)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%206-brightgreen)](https://phpstan.org)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
```

---

## 🗓️ Histórico de Progresso

| Data | Fase | Ação |
|---|---|---|
| 2026-05-17 | Fase 1 | Criado `.github/workflows/ci.yml` + ajuste `phpunit.xml` (SQLite ativado) |
| — | Fase 1 | Aguardando push para GitHub e confirmação do badge verde |
