import type { Meta, StoryObj } from '@storybook/react-vite';
import { Loader2, Plus, Trash2 } from 'lucide-react';
import { Button } from './button';

const meta: Meta<typeof Button> = {
  title: 'UI/Button',
  component: Button,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Componente de botão com múltiplas variantes e tamanhos. Baseado em shadcn/ui com tokens do Design System Velvet Intelligence.',
      },
    },
  },
  argTypes: {
    variant: {
      control: 'select',
      options: ['default', 'destructive', 'outline', 'secondary', 'ghost', 'link'],
      description: 'Variante visual do botão',
    },
    size: {
      control: 'select',
      options: ['default', 'sm', 'lg', 'icon'],
      description: 'Tamanho do botão',
    },
    disabled: {
      control: 'boolean',
    },
  },
};

export default meta;
type Story = StoryObj<typeof Button>;

export const Default: Story = {
  args: { children: 'Salvar' },
};

export const Destructive: Story = {
  args: { variant: 'destructive', children: 'Excluir' },
};

export const Outline: Story = {
  args: { variant: 'outline', children: 'Cancelar' },
};

export const Secondary: Story = {
  args: { variant: 'secondary', children: 'Secundário' },
};

export const Ghost: Story = {
  args: { variant: 'ghost', children: 'Ghost' },
};

export const Small: Story = {
  args: { size: 'sm', children: 'Pequeno' },
};

export const Large: Story = {
  args: { size: 'lg', children: 'Grande' },
};

export const WithIcon: Story = {
  args: { children: (<><Plus className="w-4 h-4" /> Novo Cliente</>) as React.ReactNode },
};

export const Loading: Story = {
  args: {
    disabled: true,
    children: (<><Loader2 className="w-4 h-4 animate-spin" /> Salvando...</>) as React.ReactNode,
  },
};

export const Disabled: Story = {
  args: { disabled: true, children: 'Desabilitado' },
};

export const DestructiveWithIcon: Story = {
  name: 'Destructive com ícone',
  args: {
    variant: 'destructive',
    children: (<><Trash2 className="w-4 h-4" /> Excluir Proposta</>) as React.ReactNode,
  },
};

/** Todas as variantes lado a lado */
export const AllVariants: Story = {
  name: 'Todas as variantes',
  render: () => (
    <div className="flex flex-wrap gap-3 items-center p-4">
      <Button>Default</Button>
      <Button variant="destructive">Destructive</Button>
      <Button variant="outline">Outline</Button>
      <Button variant="secondary">Secondary</Button>
      <Button variant="ghost">Ghost</Button>
      <Button variant="link">Link</Button>
      <Button disabled>Disabled</Button>
    </div>
  ),
};
