# Module 1: Autenticação e Layout

**Status:** ✅ 100% Completo

---

## 📋 Visão Geral

Sistema completo de autenticação com JWT via Laravel Sanctum + layout da aplicação com sidebar e header.

---

## 🔐 Backend - Autenticação

### AuthController
- ✅ 3 endpoints implementados:
  - `POST /api/auth/login` - Autenticar e receber token
  - `POST /api/auth/logout` - Revogar token
  - `GET /api/auth/me` - Dados do usuário autenticado

### Laravel Sanctum
- ✅ Configurado para API stateless (Bearer tokens)
- ✅ Token expiration: 24 horas
- ✅ Middleware: `auth:sanctum`
- ✅ Rate limiting: 5 tentativas/minuto no login

### Documentação
- ✅ Swagger completo para endpoints de auth
- ✅ Schemas de request/response
- ✅ Exemplos de uso

---

## 🎨 Frontend - UI

### LoginPage
**Design Modern Split Screen:**
- ✅ Split layout (50/50)
- ✅ Lado esquerdo: Formulário de login
- ✅ Lado direito: Features showcase
- ✅ Ícones Lucide React
- ✅ Gradiente azul
- ✅ Responsivo

**Features Showcase:**
1. 🎯 Dashboard Inteligente
2. 👥 Gestão de Clientes
3. 📊 Pipeline Kanban
4. 📈 Relatórios Detalhados

### Layout Components

**Layout.tsx:**
- Sidebar fixa à esquerda
- Header no topo
- Content area principal
- Navigation links:
  - 🏠 Dashboard
  - 👥 Clientes
  - 🎯 Oportunidades
  - 📦 Produtos
  - 📄 Propostas

**Sidebar:**
- Logo e nome da aplicação
- Links de navegação com ícones
- Active state visual
- Logout button no rodapé

**Header:**
- Breadcrumb/título da página
- User info (nome, avatar)
- Dropdown de opções (futuro)

---

## 🔒 Autenticação Frontend

### AuthContext
```typescript
interface AuthContextType {
  user: User | null;
  token: string | null;
  login: (credentials: LoginCredentials) => Promise<void>;
  logout: () => void;
  isAuthenticated: boolean;
}
```

**Features:**
- ✅ Gerencia estado do usuário logado
- ✅ Persiste token no localStorage
- ✅ Provider global para toda aplicação

### PrivateRoute
- ✅ HOC para proteção de rotas
- ✅ Redireciona para /login se não autenticado
- ✅ Wraps componentes protegidos

### Axios Interceptor
```typescript
// Request interceptor
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor (refresh token)
api.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401) {
      // Tenta refresh token
      // Se falhar, redireciona para login
    }
  }
);
```

**Features:**
- ✅ Adiciona token automaticamente
- ✅ Refresh automático em erro 401
- ✅ Fila de requests durante refresh
- ✅ Logout em caso de falha

---

## 📡 Endpoints

```bash
POST /api/auth/login
Body: { email, password }
Response: { user: {...}, token: "11|..." }

POST /api/auth/logout
Headers: Authorization: Bearer {token}
Response: { message: "Logged out successfully" }

GET /api/auth/me
Headers: Authorization: Bearer {token}
Response: { id, name, email, ... }
```

---

## ✅ Features

- ✅ Login funcional com validação
- ✅ JWT tokens (24h expiration)
- ✅ Refresh token automático
- ✅ Logout funcional
- ✅ Layout completo com sidebar
- ✅ Navegação entre páginas
- ✅ Proteção de rotas
- ✅ Rate limiting (5 tentativas/min)
- ✅ Documentação Swagger

---

## 🎯 Testes Realizados

```bash
✅ Login com credenciais corretas → 200 OK
✅ Login com credenciais erradas → 401 Unauthorized
✅ Rate limiting após 5 tentativas → 429 Too Many Requests
✅ Logout → Token revogado
✅ Access com token válido → Dados retornados
✅ Access com token expirado → Refresh automático
✅ Navegação entre páginas → Mantém autenticação
```

---

_Module 1 estabelece autenticação segura e layout profissional._
