#!/usr/bin/env bash
# =============================================================================
# Sales Management System — Setup Automatizado
# =============================================================================
# Uso: ./setup.sh [--fresh] [--no-seed] [--help]
#
#   --fresh     Derruba containers e volumes antes de subir (fresh install)
#   --no-seed   Roda migrate sem seed
#   --help      Exibe esta ajuda
# =============================================================================
set -euo pipefail

# ─── Cores ───────────────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
RESET='\033[0m'

# ─── Helpers ─────────────────────────────────────────────────────────────────
step()    { echo -e "\n${BLUE}${BOLD}==>${RESET} ${BOLD}$*${RESET}"; }
ok()      { echo -e "  ${GREEN}✔${RESET}  $*"; }
warn()    { echo -e "  ${YELLOW}⚠${RESET}  $*"; }
fail()    { echo -e "  ${RED}✖${RESET}  $*" >&2; exit 1; }
info()    { echo -e "  ${CYAN}→${RESET}  $*"; }

# ─── Opções via argumento ────────────────────────────────────────────────────
OPT_FRESH=false
OPT_NO_SEED=false

for arg in "$@"; do
  case $arg in
    --fresh)   OPT_FRESH=true  ;;
    --no-seed) OPT_NO_SEED=true ;;
    --help)
      sed -n '3,10p' "$0"
      exit 0
      ;;
    *) fail "Argumento desconhecido: $arg. Use --help para ver as opções." ;;
  esac
done

# ─── Diretório raiz do projeto ───────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# =============================================================================
echo -e "\n${BOLD}${CYAN}Sales Management System — Setup${RESET}"
echo -e "${CYAN}$(printf '%.0s─' {1..50})${RESET}"

# ─── 1. Verificar pré-requisitos ─────────────────────────────────────────────
step "Verificando pré-requisitos"

if ! command -v docker &>/dev/null; then
  fail "Docker não encontrado. Instale em https://docs.docker.com/get-docker/"
fi
ok "Docker: $(docker --version | cut -d' ' -f3 | tr -d ',')"

# Verificar docker compose plugin v2 (não o binário legado docker-compose v1)
if ! docker compose version &>/dev/null; then
  fail "Docker Compose plugin v2 não encontrado. Atualize o Docker para 25.x+."
fi
ok "Docker Compose: $(docker compose version --short)"

# ─── 2. Exportar UID do host ──────────────────────────────────────────────────
# Necessário para que o build arg 'uid' no Dockerfile receba o UID real do
# usuário, garantindo permissão de escrita nos volumes montados.
# UID é reservada e readonly no bash; usamos HOST_UID/HOST_GID como alternativa.
HOST_UID="$(id -u)"
HOST_GID="$(id -g)"
export HOST_UID HOST_GID
ok "UID do host: ${HOST_UID}"

# ─── 3. Arquivos .env ────────────────────────────────────────────────────────
step "Configurando arquivos .env"

if [[ ! -f backend/.env ]]; then
  cp backend/.env.example backend/.env
  ok "backend/.env criado a partir de .env.example"
else
  ok "backend/.env já existe — mantido sem alteração"
fi

if [[ ! -f frontend/.env ]]; then
  cp frontend/.env.example frontend/.env
  ok "frontend/.env criado a partir de .env.example"
else
  ok "frontend/.env já existe — mantido sem alteração"
fi

# ─── 4. Fresh install (opcional) ─────────────────────────────────────────────
if [[ "$OPT_FRESH" == true ]]; then
  step "Modo --fresh: derrubando containers e volumes existentes"
  docker compose down -v --remove-orphans 2>/dev/null || true
  ok "Containers e volumes removidos"
fi

# ─── 5. Build das imagens ────────────────────────────────────────────────────
step "Construindo imagens Docker"
info "Isso pode demorar alguns minutos na primeira execução..."
docker compose build --quiet
ok "Imagens construídas"

# ─── 6. Subir containers ─────────────────────────────────────────────────────
step "Iniciando containers"
docker compose up -d --remove-orphans
ok "Containers iniciados"

# ─── 7. Aguardar MySQL ───────────────────────────────────────────────────────
step "Aguardando MySQL ficar pronto"
TIMEOUT=60
ELAPSED=0
until docker compose exec -T mysql mysqladmin ping -h localhost --silent 2>/dev/null; do
  if (( ELAPSED >= TIMEOUT )); then
    fail "MySQL não ficou pronto em ${TIMEOUT}s. Verifique: docker compose logs mysql"
  fi
  printf "."
  sleep 2
  (( ELAPSED += 2 ))
done
echo ""
ok "MySQL pronto (${ELAPSED}s)"

# ─── 8. Aguardar Redis ───────────────────────────────────────────────────────
step "Aguardando Redis ficar pronto"
ELAPSED=0
until docker compose exec -T redis redis-cli ping 2>/dev/null | grep -q PONG; do
  if (( ELAPSED >= 30 )); then
    fail "Redis não ficou pronto em 30s. Verifique: docker compose logs redis"
  fi
  printf "."
  sleep 1
  (( ELAPSED += 1 ))
done
echo ""
ok "Redis pronto (${ELAPSED}s)"

# ─── 9. Dependências PHP (Composer) ──────────────────────────────────────────
step "Instalando dependências PHP (Composer)"
if [[ -d backend/vendor ]] && [[ "$OPT_FRESH" == false ]]; then
  warn "vendor/ já existe — rodando composer install para checar integridade"
fi
docker compose exec -T backend composer install --no-interaction --prefer-dist --optimize-autoloader
ok "Dependências PHP instaladas"

# ─── 10. APP_KEY ─────────────────────────────────────────────────────────────
step "Verificando APP_KEY"
APP_KEY_CURRENT=$(grep -E '^APP_KEY=' backend/.env | cut -d'=' -f2 | tr -d '"' | tr -d "'")
if [[ -z "$APP_KEY_CURRENT" ]]; then
  docker compose exec -T backend php artisan key:generate --no-interaction
  ok "APP_KEY gerada"
else
  ok "APP_KEY já configurada — mantida"
fi

# ─── 11. Migrations (+ seed opcional) ────────────────────────────────────────
step "Executando migrations"
if [[ "$OPT_FRESH" == true ]]; then
  MIGRATE_CMD="migrate:fresh"
  info "Modo fresh: recriando todas as tabelas"
else
  MIGRATE_CMD="migrate"
fi

if [[ "$OPT_NO_SEED" == true ]]; then
  docker compose exec -T backend php artisan "$MIGRATE_CMD" --no-interaction --force
  ok "Migrations executadas (sem seed)"
else
  docker compose exec -T backend php artisan "$MIGRATE_CMD" --seed --no-interaction --force
  ok "Migrations e seeds executados"
fi

# ─── 12. Cache de configurações ──────────────────────────────────────────────
step "Otimizando cache do Laravel"
docker compose exec -T backend php artisan optimize:clear --no-interaction
docker compose exec -T backend php artisan config:cache --no-interaction
ok "Cache otimizado"

# ─── 13. Dependências JS (npm) ───────────────────────────────────────────────
step "Instalando dependências JavaScript (npm)"
if [[ -d frontend/node_modules ]] && [[ "$OPT_FRESH" == false ]]; then
  warn "node_modules já existe — rodando npm install para checar integridade"
fi
docker compose exec -T frontend npm install --no-fund --no-audit
ok "Dependências JS instaladas"

# ─── 14. Status final ────────────────────────────────────────────────────────
echo ""
echo -e "${CYAN}$(printf '%.0s─' {1..50})${RESET}"
echo -e "${GREEN}${BOLD}  Setup concluído com sucesso!${RESET}"
echo -e "${CYAN}$(printf '%.0s─' {1..50})${RESET}"
echo ""
echo -e "  ${BOLD}Frontend:${RESET}    ${CYAN}http://localhost:5173${RESET}"
echo -e "  ${BOLD}Backend API:${RESET} ${CYAN}http://localhost:8000${RESET}"
echo -e "  ${BOLD}Swagger:${RESET}     ${CYAN}http://localhost:8000/api/documentation${RESET}"
echo -e "  ${BOLD}Mailhog:${RESET}     ${CYAN}http://localhost:8025${RESET}"
echo -e "  ${BOLD}MySQL:${RESET}       ${CYAN}localhost:3307${RESET}  (user: root / senha: secret)"
echo ""
  echo -e "  ${BOLD}Credenciais padrão (seed):${RESET}"
  echo -e "    Admin:   ${CYAN}admin@salesmanagement.com${RESET}  /  senha: ${CYAN}password${RESET}"
  echo -e "    Gerente: ${CYAN}gabriela.trevisan@salesmanagement.com${RESET}  /  senha: ${CYAN}password${RESET}"
echo ""
echo -e "  Para parar o ambiente: ${BOLD}docker compose down${RESET}"
echo -e "  Para logs:             ${BOLD}docker compose logs -f [backend|frontend|mysql]${RESET}"
echo ""
