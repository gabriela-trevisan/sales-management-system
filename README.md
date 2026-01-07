# 🚀 Sales Management System

Sistema completo de gestão de vendas e CRM desenvolvido com Laravel 11, React 19 e TypeScript.

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19-61DAFB?logo=react&logoColor=black)](https://react.dev)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?logo=typescript&logoColor=white)](https://typescriptlang.org)
[![Vite](https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white)](https://vitejs.dev)

---

## 📋 Sobre o Projeto

Sistema de gestão de vendas e CRM profissional com recursos avançados para gerenciamento de:

- 📊 **Pipeline de Vendas**: Funil customizável com drag-and-drop
- 👥 **Gestão de Clientes**: CRM completo com scoring RFM e segmentação
- 💰 **Propostas Comerciais**: Geração e acompanhamento de propostas
- 💵 **Cálculo de Comissões**: Regras configuráveis e automáticas
- 📈 **Dashboard Analítico**: Métricas, gráficos e forecast de vendas
- 🤖 **Automação**: Workflows automatizados e follow-ups inteligentes

---

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP** 8.3
- **Laravel** 11.47
- **MySQL** 9.0
- **Redis** 7.2
- **PHPUnit** 11.x

### Frontend
- **React** 19.x
- **TypeScript** 5.x
- **Vite** 7.3
- **Tailwind CSS** (a ser instalado)
- **React Router** (a ser instalado)

### Infraestrutura
- **Docker** & **Docker Compose**
- **Nginx** 1.27-alpine
- **Mailhog** (email testing)

---

## 📦 Pré-requisitos

Para rodar o projeto, você precisa ter instalado:

- [Docker](https://www.docker.com/get-started) (25.x+)
- [Docker Compose](https://docs.docker.com/compose/install/) (v2.x+)

**Ou**, para desenvolvimento local:
- PHP 8.2+ ([Download](https://www.php.net/downloads))
- Composer 2.7+ ([Download](https://getcomposer.org/download/))
- Node.js 22+ ([Download](https://nodejs.org/))
- MySQL 9.0 ou 8.4+
- Redis 7.2+

---

## 🚀 Instalação e Configuração

### Opção 1: Usando Docker (Recomendado)

1. **Clone o repositório:**
```bash
git clone https://github.com/gabriela-trevisan/sales-management-system.git
cd sales-management-system
```

2. **Configure o ambiente:**
```bash
# Backend
cp backend/.env.example backend/.env

# Frontend
cp frontend/.env.example frontend/.env
```

3. **Suba os containers:**
```bash
docker-compose up -d
```

4. **Instale as dependências dentro do container:**
```bash
# Backend
docker-compose exec backend composer install
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate --seed

# Frontend
docker-compose exec frontend npm install
```

5. **Acesse a aplicação:**
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000
- **Mailhog**: http://localhost:8025

---

### Opção 2: Desenvolvimento Local

1. **Clone o repositório:**
```bash
git clone https://github.com/gabriela-trevisan/sales-management-system.git
cd sales-management-system
```

2. **Configure o Backend:**
```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

3. **Configure o Frontend:**
```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

4. **Acesse:**
- **Frontend**: http://localhost:5173
- **Backend**: http://localhost:8000

---

## 📚 Estrutura do Projeto

```
sales-management-system/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Domain/         # Camada de domínio (DDD)
│   │   ├── Application/    # Casos de uso
│   │   ├── Infrastructure/ # Implementações técnicas
│   │   └── Presentation/   # Controllers e API
│   ├── database/
│   ├── tests/
│   └── Dockerfile
├── frontend/               # React SPA
│   ├── src/
│   │   ├── components/    # Componentes reutilizáveis
│   │   ├── features/      # Features por módulo
│   │   ├── hooks/         # Custom hooks
│   │   ├── services/      # API services
│   │   └── utils/         # Utilitários
│   └── Dockerfile
├── docker/
│   └── nginx/             # Configurações Nginx
├── docker-compose.yml     # Orquestração dos containers
├── DEVELOPMENT_GUIDE.md   # Guia de desenvolvimento
└── README.md              # Este arquivo
```

---

## 🧪 Testes

### Backend (PHPUnit)

```bash
# Com Docker
docker-compose exec backend php artisan test

# Local
cd backend
php artisan test

# Com coverage
php artisan test --coverage
```

### Frontend (Vitest)

```bash
# Com Docker
docker-compose exec frontend npm test

# Local
cd frontend
npm test

# Com coverage
npm run test:coverage
```

---

## 📖 Documentação da API

A documentação completa da API estará disponível em:
- **Swagger UI**: http://localhost:8000/api/documentation (a ser implementado)

---

## 🎯 Funcionalidades Principais

### ✅ Implementadas
- ✅ Setup inicial do projeto
- ✅ Configuração Docker
- ✅ Estrutura base Laravel e React

### 🚧 Em Desenvolvimento
- 🚧 Autenticação JWT
- 🚧 CRUD de Clientes
- 🚧 Pipeline de Vendas (Kanban)
- 🚧 Gestão de Propostas
- 🚧 Dashboard com gráficos

### 📋 Próximas Features
- Cálculo de comissões
- Scoring RFM de clientes
- Automação de vendas
- Previsão de vendas (forecast)
- Sistema de notificações
- Relatórios exportáveis

---

## 🤝 Contribuição

Este é um projeto de portfólio pessoal. Se você encontrou algum problema ou tem sugestões, sinta-se à vontade para abrir uma issue.

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👨‍💻 Autor

**Gabriela Trevisan**

- GitHub: [@gabriela-trevisan](https://github.com/gabriela-trevisan)
- LinkedIn: [Seu LinkedIn](https://linkedin.com/in/seu-perfil)

---

## 📚 Documentação Adicional

- [DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md) - Guia completo de desenvolvimento
- [PORTFOLIO_PROJECTS_PLAN.md](PORTFOLIO_PROJECTS_PLAN.md) - Planejamento do portfólio

---

**⭐ Se este projeto foi útil para você, considere dar uma estrela no repositório!**
