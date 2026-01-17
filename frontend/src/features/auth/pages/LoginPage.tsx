import { useState, FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/common/Button';
import { Input } from '@/components/common/Input';
import { Alert } from '@/components/common/Alert';
import { Users, TrendingUp, Target, BarChart3 } from 'lucide-react';

export function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setIsLoading(true);

    try {
      await login({ email, password });
      navigate('/dashboard');
    } catch (err: any) {
      setError(err.response?.data?.message || 'Erro ao fazer login. Verifique suas credenciais.');
    } finally {
      setIsLoading(false);
    }
  };

  const features = [
    {
      icon: Users,
      title: 'Gestão de Clientes',
      description: 'Centralize todos os dados dos seus clientes em um único lugar'
    },
    {
      icon: Target,
      title: 'Pipeline de Vendas',
      description: 'Acompanhe suas oportunidades em tempo real com Kanban intuitivo'
    },
    {
      icon: TrendingUp,
      title: 'Análise de Desempenho',
      description: 'Métricas e relatórios detalhados para impulsionar seus resultados'
    },
    {
      icon: BarChart3,
      title: 'Dashboard Inteligente',
      description: 'Visualize KPIs importantes e tome decisões baseadas em dados'
    }
  ];

  return (
    <div className="min-h-screen flex flex-col lg:flex-row">
      {/* Left Side - Branding & Features */}
      <div className="lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-8 lg:p-12 flex flex-col justify-between text-white">
        <div>
          <div className="flex items-center gap-3 mb-12">
            <div className="bg-white/10 backdrop-blur-sm p-2 rounded-lg">
              <TrendingUp className="w-8 h-8" />
            </div>
            <div>
              <h1 className="text-2xl font-bold">Sales Management</h1>
              <p className="text-blue-200 text-sm">Sistema de CRM Profissional</p>
            </div>
          </div>

          <div className="space-y-8">
            <div>
              <h2 className="text-3xl lg:text-4xl font-bold mb-4">
                Gerencie suas vendas de forma inteligente
              </h2>
              <p className="text-blue-100 text-lg">
                Aumente sua produtividade e converta mais leads em clientes com nossa plataforma completa de CRM
              </p>
            </div>

            <div className="grid gap-6">
              {features.map((feature) => (
                <div key={feature.title} className="flex gap-4 items-start">
                  <div className="bg-white/10 backdrop-blur-sm p-3 rounded-lg flex-shrink-0">
                    <feature.icon className="w-6 h-6" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-lg mb-1">{feature.title}</h3>
                    <p className="text-blue-100 text-sm">{feature.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div className="mt-8 pt-8 border-t border-white/20">
          <p className="text-blue-200 text-sm">
            © 2026 Sales Management System. Desenvolvido com 💙
          </p>
        </div>
      </div>

      {/* Right Side - Login Form */}
      <div className="lg:w-1/2 flex items-center justify-center p-8 lg:p-12 bg-gray-50">
        <div className="w-full max-w-md">
          <div className="mb-8">
            <h2 className="text-3xl font-bold text-gray-900 mb-2">
              Bem-vindo de volta
            </h2>
            <p className="text-gray-600">
              Entre com suas credenciais para acessar o sistema
            </p>
          </div>

          {error && (
            <div className="mb-6">
              <Alert type="error" onClose={() => setError('')}>
                {error}
              </Alert>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-5">
            <Input
              type="email"
              label="Email"
              placeholder="seu@email.com"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              autoComplete="email"
            />

            <Input
              type="password"
              label="Senha"
              placeholder="••••••••"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              autoComplete="current-password"
            />

            <div className="flex items-center justify-between text-sm">
              <label className="flex items-center gap-2 cursor-pointer">
                <input 
                  type="checkbox" 
                  className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <span className="text-gray-700">Lembrar-me</span>
              </label>
              <a href="#" className="text-blue-600 hover:text-blue-700 font-medium">
                Esqueceu a senha?
              </a>
            </div>

            <Button
              type="submit"
              className="w-full"
              isLoading={isLoading}
            >
              Entrar
            </Button>
          </form>

          <div className="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-100">
            <p className="text-sm text-blue-900 font-medium mb-2">Credenciais de teste:</p>
            <div className="space-y-1 text-sm">
              <p className="text-blue-700">
                <span className="font-semibold">Email:</span> admin@salesmanagement.com
              </p>
              <p className="text-blue-700">
                <span className="font-semibold">Senha:</span> password
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
