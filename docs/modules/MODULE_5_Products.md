# Module 5: CRUD de Produtos

**Status:** ✅ 100% Completo

---

## 📋 Visão Geral

Sistema de gerenciamento de produtos/serviços seguindo arquitetura DDD estabelecida.

---

## 🏗️ Arquitetura Backend (13 arquivos)

### Domain Layer
- `Product.php` - Model com SoftDeletes, casts de decimals
- `ProductCategory.php` - Categorias de produtos
- `ProductRepositoryInterface.php` - Interface

### Infrastructure
- `EloquentProductRepository.php` - Eager loading de category

### Application (CQRS)
- `CreateProductCommand.php` - DTO readonly
- `CreateProductHandler.php` - Use case

### Presentation
- `ProductController.php` - 5 endpoints RESTful
- `ProductCategoryController.php` - Lista categorias
- `CreateProductRequest.php`, `UpdateProductRequest.php`
- `ProductResource.php` - JSON Resource
- `ProductSchema.php` - OpenAPI schema

---

## 🎨 Frontend (7 arquivos)

### Services
- `productService.ts` - Cliente API
- `categoryService.ts` - Cliente categorias

### Schemas
- `productSchema.ts` - Zod com regex para SKU

### Components
- `ProductFormModal.tsx` - Formulário completo
- `ProductListPage.tsx` - Lista com filtros

---

## 📦 Estrutura de Produtos

### Campos
- `name` - Nome do produto/serviço
- `sku` - Código único (A-Z0-9-_)
- `description` - Descrição detalhada
- `category_id` - FK para categorias
- `base_price` - Preço base (decimal 15,2)
- `cost_price` - Custo (decimal 15,2)
- `unit` - Unidade de medida (enum)
- `is_active` - Ativo/inativo
- `requires_approval` - Requer aprovação
- `specifications` - JSON customizável

### Unidades de Medida
- `unit` - Unidade
- `kg` - Quilograma
- `liter` - Litro
- `meter` - Metro
- `hour` - Hora
- `month` - Mês

### Categorias (8)
1. Software Development
2. Hardware
3. Serviços Profissionais
4. Suporte Técnico
5. Treinamento
6. Cloud Services
7. Licenças
8. Infraestrutura

---

## 📡 Endpoints

```bash
GET    /api/products?search=&is_active=&category_id=&page=1&per_page=15
POST   /api/products
GET    /api/products/{id}
PUT    /api/products/{id}
DELETE /api/products/{id}
GET    /api/product-categories
```

---

## ✨ Features

### Backend
- ✅ CRUD completo
- ✅ Soft deletes
- ✅ Eager loading (category)
- ✅ Filtros: search, is_active, category_id
- ✅ Paginação
- ✅ SKU unique com regex validation
- ✅ Specifications como JSON
- ✅ Repository pattern + CQRS
- ✅ Documentação Swagger

### Frontend
- ✅ Lista com filtros (busca, status, categoria)
- ✅ Paginação
- ✅ Modal de formulário
- ✅ Validação Zod
- ✅ SKU uppercase + regex
- ✅ Dropdown de categorias carregadas
- ✅ Dropdown de unidades
- ✅ Checkboxes: is_active, requires_approval
- ✅ Status badges (Ativo/Inativo)
- ✅ Formatação de preços (R$)
- ✅ ConfirmDialog para exclusão

---

## 🎯 Regras de Negócio

1. **SKU Único:**
   - Formato: A-Z, 0-9, -, _
   - Convertido para uppercase
   - Validação no backend e frontend

2. **Preços:**
   - base_price obrigatório
   - cost_price opcional
   - Ambos decimal(15,2)
   - Não podem ser negativos

3. **Ativo/Inativo:**
   - Produtos inativos não aparecem em dropdowns
   - Filtro de status na listagem

4. **Aprovação:**
   - Flag `requires_approval` para produtos especiais
   - Útil para produtos que precisam validação antes de venda

---

## 📊 Dados Seedados

Produtos realistas para consultoria:
- **Horas Técnicas:**
  - Arquiteto de Software (R$ 350/h)
  - Dev Sênior (R$ 250/h)
  - Dev Pleno (R$ 180/h)
  - Dev Júnior (R$ 120/h)
  - QA/Tester (R$ 160/h)
  - Scrum Master (R$ 220/h)
  - DevOps (R$ 280/h)
  - UX/UI (R$ 200/h)

- **Pacotes:**
  - Discovery (40h): R$ 12.000
  - MVP (320h): R$ 80.000
  - Squad Dedicado (160h/mês): R$ 35.000

- **Suporte:**
  - Evolutivo (40h/mês): R$ 9.000
  - Corretivo 24x7: R$ 15.000

---

## 🧪 Validações

```bash
✅ SKU regex: DEV-SR-001 ✅
✅ SKU regex: dev sr 001 ❌
✅ Preço base obrigatório
✅ Preço negativo rejeitado
✅ Unidade enum validada
✅ Categoria exists validada
✅ Soft delete funciona
✅ Filtros funcionam
✅ Eager loading otimizado
```

---

## 🎨 UI/UX

### ProductFormModal
- Nome, SKU, Descrição
- Categoria (dropdown)
- Unidade (dropdown)
- Preço Base, Custo
- Checkboxes: Ativo, Requer Aprovação
- Validação inline

### ProductListPage
- Colunas: Produto, SKU, Categoria, Preço Base, Unidade, Status, Ações
- Filtros: Busca, Status, Categoria
- Badges verde (Ativo) / cinza (Inativo)
- Formatação de preços em R$
- Label de unidade traduzida

---

_Module 5 completa o catálogo de produtos para geração de propostas._
