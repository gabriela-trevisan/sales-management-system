import type { Meta, StoryObj } from '@storybook/react-vite';
import { Alert } from './Alert';

const meta: Meta<typeof Alert> = {
  title: 'UI/Alert',
  component: Alert,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Mensagem de feedback contextual com variantes semânticas. Suporta dismiss via `onClose`.',
      },
    },
  },
  argTypes: {
    type: {
      control: 'select',
      options: ['success', 'error', 'warning', 'info'],
    },
  },
};

export default meta;
type Story = StoryObj<typeof Alert>;

export const Success: Story = {
  args: {
    type: 'success',
    children: 'Cliente salvo com sucesso.',
  },
};

export const Error: Story = {
  args: {
    type: 'error',
    children: 'Credenciais inválidas. Verifique seu e-mail e senha.',
  },
};

export const Warning: Story = {
  args: {
    type: 'warning',
    children: 'Esta proposta expira em 3 dias.',
  },
};

export const Info: Story = {
  args: {
    type: 'info',
    children: 'Preencha todos os campos obrigatórios antes de enviar.',
  },
};

export const WithDismiss: Story = {
  name: 'Com dismiss',
  args: {
    type: 'error',
    children: 'Erro ao carregar dados. Tente novamente.',
    onClose: () => alert('dismiss!'),
  },
};

export const AllTypes: Story = {
  name: 'Todos os tipos',
  render: () => (
    <div className="space-y-3 w-96 p-4">
      <Alert type="success">Operação realizada com sucesso.</Alert>
      <Alert type="info">Informação importante para o usuário.</Alert>
      <Alert type="warning">Atenção: esta ação não pode ser desfeita.</Alert>
      <Alert type="error">Ocorreu um erro. Contate o suporte.</Alert>
    </div>
  ),
};
