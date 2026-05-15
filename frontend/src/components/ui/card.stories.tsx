import type { Meta, StoryObj } from '@storybook/react-vite';
import { TrendingUp, Users, DollarSign, Target } from 'lucide-react';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from './card';
import { Button } from './button';

const meta: Meta<typeof Card> = {
  title: 'UI/Card',
  component: Card,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Container de conteúdo com semântica de elevação. Usado em MetricCards, painéis de gráfico, formulários modais e listagens.',
      },
    },
  },
};

export default meta;
type Story = StoryObj<typeof Card>;

export const Default: Story = {
  render: () => (
    <Card className="w-80">
      <CardHeader>
        <CardTitle>Título do Card</CardTitle>
        <CardDescription>Descrição secundária do conteúdo.</CardDescription>
      </CardHeader>
      <CardContent>
        <p className="text-sm text-muted-foreground">
          Conteúdo principal do card. Pode conter qualquer elemento.
        </p>
      </CardContent>
    </Card>
  ),
};

export const WithFooter: Story = {
  name: 'Com footer',
  render: () => (
    <Card className="w-80">
      <CardHeader>
        <CardTitle>Proposta #PRO-2025-001</CardTitle>
        <CardDescription>Acme Corp — Desenvolvimento de MVP</CardDescription>
      </CardHeader>
      <CardContent>
        <p className="text-2xl font-bold">R$ 48.000</p>
        <p className="text-sm text-muted-foreground">Válida até 30/06/2026</p>
      </CardContent>
      <CardFooter className="gap-2">
        <Button size="sm">Aprovar</Button>
        <Button size="sm" variant="outline">Ver detalhes</Button>
      </CardFooter>
    </Card>
  ),
};

export const MetricCard: Story = {
  name: 'Metric Card',
  render: () => (
    <div className="grid grid-cols-2 gap-4 p-4">
      {[
        { title: 'Total Clientes',   value: '248',      icon: Users,      color: 'text-primary'     },
        { title: 'Pipeline',         value: 'R$ 1.2M',  icon: DollarSign, color: 'text-info'        },
        { title: 'Conversão',        value: '32.4%',    icon: TrendingUp, color: 'text-success'     },
        { title: 'Oportunidades',    value: '37',       icon: Target,     color: 'text-warning'     },
      ].map((item) => (
        <Card key={item.title} className="hover:shadow-md transition-shadow">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              {item.title}
            </CardTitle>
            <item.icon className={`h-5 w-5 ${item.color}`} />
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold">{item.value}</p>
          </CardContent>
        </Card>
      ))}
    </div>
  ),
};
