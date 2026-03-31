# Design System - Material Design com shadcn/ui

**Versão:** 1.0  
**Status:** ✅ Implementado

---

## 📋 Visão Geral

Design System completo baseado em **Material Design** do shadcn/ui, utilizando **OKLCH color space** para melhor consistência e interpolação de cores.

---

## 🎨 Color System

### OKLCH vs HSL

**Por que OKLCH?**
- ✅ **Consistência Perceptual**: Cores com mesma luminosidade parecem igualmente brilhantes
- ✅ **Interpolação Natural**: Transições de cores mais suaves e naturais
- ✅ **Wide Color Gamut**: Suporte a telas modernas com maior gama de cores
- ✅ **Acessibilidade**: Melhor controle de luminosidade para WCAG compliance

### Paleta de Cores

#### Light Mode
```css
--background: oklch(0.98 0.01 335.69);     /* Fundo principal */
--foreground: oklch(0.22 0 0);             /* Texto principal */
--card: oklch(0.96 0.01 335.69);           /* Fundo de cards */
--primary: oklch(0.51 0.21 286.50);        /* Cor primária (roxo) */
--success: oklch(0.60 0.15 145);           /* Cor de sucesso (verde) */
--warning: oklch(0.70 0.15 85);            /* Cor de aviso (laranja) */
--destructive: oklch(0.57 0.23 29.21);     /* Cor de erro (vermelho) */
```

#### Dark Mode
```css
--background: oklch(0.15 0.01 317.69);     /* Fundo escuro */
--foreground: oklch(0.95 0.01 321.50);     /* Texto claro */
--card: oklch(0.22 0.02 322.13);           /* Fundo de cards escuro */
--primary: oklch(0.60 0.22 279.81);        /* Cor primária ajustada */
--success: oklch(0.55 0.17 145);           /* Verde mais suave */
--warning: oklch(0.65 0.15 85);            /* Laranja mais suave */
```

### Chart Colors
```css
--chart-1: oklch(0.61 0.21 279.42);  /* Roxo para gráficos */
--chart-2: oklch(0.72 0.15 157.67);  /* Verde para gráficos */
--chart-3: oklch(0.66 0.17 324.24);  /* Rosa para gráficos */
--chart-4: oklch(0.81 0.15 127.91);  /* Verde claro */
--chart-5: oklch(0.68 0.17 258.25);  /* Azul */
```

### Sidebar Colors
```css
/* Light Mode Sidebar */
--sidebar: oklch(0.99 0 0);                    /* Fundo branco */
--sidebar-foreground: oklch(0.15 0 0);         /* Texto escuro */
--sidebar-primary: oklch(0.56 0.11 228.27);    /* Azul primário */
--sidebar-accent: oklch(0.95 0 0);             /* Hover state */
--sidebar-border: oklch(0.90 0 0);             /* Borda */

/* Dark Mode Sidebar */
--sidebar: oklch(0.20 0.01 317.74);            /* Fundo escuro */
--sidebar-foreground: oklch(0.95 0.01 321.50); /* Texto claro */
--sidebar-accent: oklch(0.30 0.01 319.52);     /* Hover state escuro */
```

---

## 🔤 Typography

### Fontes

**Sans-Serif (Roboto):**
- Weights: 300 (Light), 400 (Regular), 500 (Medium), 700 (Bold)
- Uso: Interface, corpo de texto, UI elements
- Variable: `--font-sans`

**Serif (Merriweather):**
- Weights: 300 (Light), 400 (Regular), 700 (Bold)
- Uso: Headings especiais, citações
- Variable: `--font-serif`

**Monospace (Geist Mono):**
- Uso: Código, dados técnicos
- Variable: `--font-mono`

### Font Features
```css
font-feature-settings: 'rlig' 1, 'calt' 1;  /* Ligaduras e alternativas contextuais */
font-synthesis: none;                        /* Sem síntese de fonte */
text-rendering: optimizeLegibility;          /* Melhor renderização */
-webkit-font-smoothing: antialiased;         /* Suavização no WebKit */
-moz-osx-font-smoothing: grayscale;          /* Suavização no Firefox */
```

### Headings
```css
h1, h2, h3, h4, h5, h6 {
  font-weight: 500;
  line-height: 1.2;
}
```

---

## 📦 Componentes shadcn/ui

### Instalados

| Componente | Versão | Radix UI Base | Uso |
|------------|--------|---------------|-----|
| **button** | 1.0 | - | Botões de ação |
| **card** | 1.0 | - | Containers de conteúdo |
| **dialog** | 1.0 | @radix-ui/react-dialog | Modais |
| **dropdown-menu** | 1.0 | @radix-ui/react-dropdown-menu | Menus contextuais |
| **badge** | 1.0 | - | Tags e status |
| **skeleton** | 1.0 | - | Loading states |
| **input** | 1.0 | - | Campos de texto |
| **textarea** | 1.0 | - | Texto multilinha |
| **label** | 1.0 | - | Labels de formulário |
| **table** | 1.0 | - | Tabelas de dados |
| **select** | 1.0 | @radix-ui/react-select | Seletores |
| **toast** | 1.0 | @radix-ui/react-toast | Notificações |

### Características

- ✅ **Acessibilidade**: WCAG 2.1 AA compliant
- ✅ **Keyboard Navigation**: Suporte completo a navegação por teclado
- ✅ **Screen Reader**: Atributos ARIA adequados
- ✅ **Focus Management**: Gestão inteligente de foco
- ✅ **Theme-aware**: Suporte a light/dark mode
- ✅ **Customizável**: CSS variables + Tailwind classes

---

## 🎭 Theme System

### Implementação

**Arquivo principal:** `frontend/src/index.css`

```css
@import "tailwindcss";

@layer base {
  :root {
    /* Cores light mode */
  }
  
  .dark {
    /* Cores dark mode */
  }
}

@theme {
  /* Mapeamento Tailwind → CSS Variables */
  --color-background: var(--background);
  --color-primary: var(--primary);
  /* ... */
}
```

### Context Provider

**Arquivo:** `frontend/src/contexts/ThemeContext.tsx`

```typescript
type Theme = 'dark' | 'light' | 'system';

interface ThemeProviderState {
  theme: Theme;
  setTheme: (theme: Theme) => void;
}

export function ThemeProvider({
  children,
  defaultTheme = 'system',
  storageKey = 'vite-ui-theme',
}: ThemeProviderProps) {
  // Gerencia tema com localStorage
  // Detecta preferência do sistema
  // Aplica classe .dark no html
}
```

### Uso

```tsx
import { useTheme } from '@/contexts/ThemeContext';

function Component() {
  const { theme, setTheme } = useTheme();
  
  return (
    <button onClick={() => setTheme(theme === 'dark' ? 'light' : 'dark')}>
      Toggle Theme
    </button>
  );
}
```

---

## 🔧 Utility Classes

### Success Colors
```css
.bg-success              /* Background verde */
.bg-success/10           /* 10% opacidade com color-mix() */
.text-success            /* Texto verde */
.text-success-foreground /* Texto contrastante */
```

### Warning Colors
```css
.bg-warning              /* Background laranja */
.bg-warning/10           /* 10% opacidade */
.text-warning            /* Texto laranja */
.text-warning-foreground /* Texto contrastante */
```

### Border Radius
```css
--radius-sm: calc(var(--radius) - 4px);  /* 12px */
--radius-md: calc(var(--radius) - 2px);  /* 14px */
--radius-lg: var(--radius);              /* 16px */
--radius-xl: calc(var(--radius) + 4px);  /* 20px */
```

### Shadows
```css
--shadow-xs: 0px 1px 3px 0px oklch(0 0 0 / 0.01);
--shadow-sm: 0px 1px 3px 0px oklch(0 0 0 / 0.01), 0px 1px 2px -1px oklch(0 0 0 / 0.01);
--shadow-md: 0px 1px 3px 0px oklch(0 0 0 / 0.01), 0px 2px 4px -1px oklch(0 0 0 / 0.01);
/* ... */
```

---

## 📱 Responsividade

### Breakpoints (Tailwind)
```css
sm: 640px   /* Small devices */
md: 768px   /* Medium devices */
lg: 1024px  /* Large devices */
xl: 1280px  /* Extra large devices */
2xl: 1536px /* 2x extra large devices */
```

### Mobile First
- Todas as classes são mobile-first
- Use prefixos `md:`, `lg:` para telas maiores
- Grid adaptativo: `grid-cols-1 md:grid-cols-2 lg:grid-cols-4`

---

## ♿ Acessibilidade

### Diretrizes WCAG 2.1 AA

**Contraste de Cores:**
- ✅ Texto normal: 4.5:1 mínimo
- ✅ Texto grande: 3:1 mínimo
- ✅ Componentes UI: 3:1 mínimo

**Navegação por Teclado:**
- ✅ Tab order lógico
- ✅ Focus visível em todos os elementos interativos
- ✅ Shortcuts documentados

**Screen Readers:**
- ✅ Labels descritivos
- ✅ ARIA attributes adequados
- ✅ Landmarks semânticos

---

## 🎯 Boas Práticas

### Uso de CSS Variables

✅ **Correto:**
```tsx
<div className="bg-primary text-primary-foreground">
<div className="bg-success/10 text-success">
<LineChart stroke="var(--chart-1)" />
```

❌ **Evitar:**
```tsx
<div className="bg-purple-500 dark:bg-purple-900">
<div className="bg-green-100 dark:bg-green-800">
<LineChart stroke="hsl(var(--chart-1))" />
```

### Color Opacity

✅ **Usar color-mix():**
```css
.bg-success\/10 {
  background-color: color-mix(in oklch, var(--success) 10%, transparent);
}
```

❌ **Evitar HSL opacity:**
```css
.bg-success\/10 {
  background-color: hsl(var(--success) / 0.1);
}
```

### Component Composition

```tsx
// ✅ Usar componentes shadcn/ui
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

<Card>
  <CardHeader>
    <CardTitle>Título</CardTitle>
  </CardHeader>
  <CardContent>
    Conteúdo
  </CardContent>
</Card>

// ❌ Criar estruturas manualmente
<div className="rounded-lg border bg-card">
  <div className="p-6">
    <h3>Título</h3>
  </div>
  <div className="p-6 pt-0">
    Conteúdo
  </div>
</div>
```

---

## 📊 Performance

### CSS Bundle
- **Tamanho:** 56.17 kB
- **Gzipped:** 10.09 kB
- **Build time:** ~16s

### Otimizações
- ✅ PurgeCSS automático via Tailwind
- ✅ CSS minificado em produção
- ✅ Variáveis reutilizáveis
- ✅ Classes utility cacheable

---

## 🚀 Próximos Passos

### Curto Prazo
- [ ] Adicionar componente `form` (react-hook-form wrapper)
- [ ] Adicionar componente `tabs` para navegação
- [ ] Adicionar componente `popover` para tooltips avançados

### Médio Prazo
- [ ] Implementar `data-table` com sorting e filtering
- [ ] Adicionar `command` palette para navegação rápida
- [ ] Criar variants de botões especializados (loading, icon-only)

### Longo Prazo
- [ ] Documentação Storybook
- [ ] Testes de acessibilidade automatizados
- [ ] Theme builder customizável

---

_Design System implementado seguindo as melhores práticas de acessibilidade, performance e manutenibilidade._
