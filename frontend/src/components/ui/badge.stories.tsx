import type { Meta, StoryObj } from '@storybook/react-vite';
import { Badge } from './badge';

const meta: Meta<typeof Badge> = {
  title: 'UI/Badge',
  component: Badge,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Rótulos compactos para status, categorias e contadores. Usados em status de clientes, propostas e pipelines.',
      },
    },
  },
  argTypes: {
    variant: {
      control: 'select',
      options: ['default', 'secondary', 'destructive', 'outline'],
    },
  },
};

export default meta;
type Story = StoryObj<typeof Badge>;

export const Default: Story = {
  args: { children: 'Ativo' },
};

export const Secondary: Story = {
  args: { variant: 'secondary', children: 'Rascunho' },
};

export const Destructive: Story = {
  args: { variant: 'destructive', children: 'Encerrado' },
};

export const Outline: Story = {
  args: { variant: 'outline', children: 'Prospect' },
};

/** Badges de status de cliente — uso real na CustomerListPage */
export const CustomerStatus: Story = {
  name: 'Status de Cliente',
  render: () => (
    <div className="flex flex-wrap gap-2 p-4">
      <span className="inline-flex items-center rounded-sm px-2 py-0.5 text-xs font-medium bg-success/10 text-success border border-success/30">
        Ativo
      </span>
      <span className="inline-flex items-center rounded-sm px-2 py-0.5 text-xs font-medium bg-muted text-muted-foreground border border-border">
        Inativo
      </span>
      <span className="inline-flex items-center rounded-sm px-2 py-0.5 text-xs font-medium bg-primary/10 text-primary border border-primary/20">
        Prospect
      </span>
      <span className="inline-flex items-center rounded-sm px-2 py-0.5 text-xs font-medium bg-destructive/10 text-destructive border border-destructive/20">
        Churned
      </span>
    </div>
  ),
};

/** Badges de status de proposta — uso real na ProposalListPage */
export const ProposalStatus: Story = {
  name: 'Status de Proposta',
  render: () => (
    <div className="flex flex-wrap gap-2 p-4">
      <span className="inline-flex items-center rounded-sm px-2 py-0.5 text-xs font-medium bg-muted text-muted-foreground">
        Rascunho
      </span>
      <span className="inline-flex items-center rounded-sm px-2 py-0.5 text-xs font-medium bg-info/10 text-info">
        Enviada
      </span>
      <span className="inline-flex items-center rounded-sm px-2 py-0.5 text-xs font-medium bg-success/10 text-success">
        Aprovada
      </span>
      <span className="inline-flex items-center rounded-sm px-2 py-0.5 text-xs font-medium bg-destructive/10 text-destructive">
        Rejeitada
      </span>
      <span className="inline-flex items-center rounded-sm px-2 py-0.5 text-xs font-medium bg-warning/10 text-warning">
        Expirada
      </span>
    </div>
  ),
};
