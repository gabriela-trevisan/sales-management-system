import type { Meta, StoryObj } from '@storybook/react-vite';
import { Input } from './input';

const meta: Meta<typeof Input> = {
  title: 'UI/Input',
  component: Input,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Input de texto com suporte a todos os tipos HTML. Use com o componente `Label` para acessibilidade.',
      },
    },
  },
  argTypes: {
    type: {
      control: 'select',
      options: ['text', 'email', 'password', 'number', 'search', 'tel'],
    },
    disabled: { control: 'boolean' },
    placeholder: { control: 'text' },
  },
};

export default meta;
type Story = StoryObj<typeof Input>;

export const Default: Story = {
  args: { placeholder: 'Digite algo...', type: 'text' },
};

export const Email: Story = {
  args: { placeholder: 'seu@email.com', type: 'email' },
};

export const Password: Story = {
  args: { placeholder: '••••••••', type: 'password' },
};

export const WithValue: Story = {
  name: 'Com valor',
  args: { defaultValue: 'Acme Corp Ltda', type: 'text' },
};

export const Disabled: Story = {
  name: 'Desabilitado',
  args: { placeholder: 'Campo desabilitado', disabled: true },
};

/** Estados lado a lado — simula label + error */
export const WithError: Story = {
  name: 'Com erro (wrapper)',
  render: () => (
    <div className="space-y-1.5 w-72">
      <label className="text-sm font-medium text-foreground" htmlFor="email-error">
        Email
      </label>
      <Input
        id="email-error"
        type="email"
        placeholder="seu@email.com"
        className="border-destructive focus-visible:ring-destructive"
        defaultValue="email-invalido"
      />
      <p className="text-sm text-destructive">Endereço de e-mail inválido.</p>
    </div>
  ),
};

export const AllStates: Story = {
  name: 'Todos os estados',
  render: () => (
    <div className="space-y-3 w-72 p-4">
      <Input placeholder="Default" />
      <Input defaultValue="Com valor" />
      <Input placeholder="Desabilitado" disabled />
      <Input
        defaultValue="Com erro"
        className="border-destructive focus-visible:ring-destructive"
      />
    </div>
  ),
};
