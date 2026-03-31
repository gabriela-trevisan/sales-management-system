import { useState, type FormEvent } from 'react';
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
    } catch (err) {
      const error = err as { response?: { data?: { message?: string } } };
      setError(error.response?.data?.message || 'Erro ao fazer login. Verifique suas credenciais.');
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
      <div className="lg:w-1/2 bg-primary p-8 lg:p-12 flex flex-col justify-between text-primary-foreground">
        <div>
          <div className="flex items-center gap-3 mb-12">
            <div className="bg-primary-foreground/10 backdrop-blur-sm p-2 rounded-lg">
              <TrendingUp className="w-8 h-8" />
            </div>
            <div>
              <h1 className="text-2xl font-bold">Sales Management</h1>
              <p className="text-primary-foreground/80 text-sm">Sistema de CRM Profissional</p>
            </div>
          </div>

          <div className="space-y-8">
            <div>
              <h2 className="text-3xl lg:text-4xl font-bold mb-4">
                Gerencie suas vendas de forma inteligente
              </h2>
              <p className="text-primary-foreground/90 text-lg">
                Aumente sua produtividade e converta mais leads em clientes com nossa plataforma completa de CRM
              </p>
            </div>

            <div className="grid gap-6">
              {features.map((feature) => (
                <div key={feature.title} className="flex gap-4 items-start">
                  <div className="bg-primary-foreground/10 backdrop-blur-sm p-3 rounded-lg flex-shrink-0">
                    <feature.icon className="w-6 h-6" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-lg mb-1">{feature.title}</h3>
                    <p className="text-primary-foreground/80 text-sm">{feature.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div className="mt-8 pt-8 border-t border-primary-foreground/20">
          <p className="text-primary-foreground/80 text-sm">
            © 2026 Sales Management System. Desenvolvido com 💙
          </p>
        </div>
      </div>

      {/* Right Side - Login Form */}
      <div className="lg:w-1/2 flex items-center justify-center p-8 lg:p-12 bg-background">
        <div className="w-full max-w-md">
          <div className="mb-8">
            <h2 className="text-3xl font-bold text-foreground mb-2">
              Bem-vindo de volta
            </h2>
            <p className="text-muted-foreground">
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
                  className="w-4 h-4 text-primary border-input rounded focus:ring-ring"
                />
                <span className="text-foreground">Lembrar-me</span>
              </label>
              <a href="#" className="text-primary hover:text-primary/90 font-medium">
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

          <div className="mt-8 p-4 bg-primary/10 rounded-lg border border-primary/20">
            <p className="text-sm text-foreground font-medium mb-2">Credenciais de teste:</p>
            <div className="space-y-1 text-sm">
              <p className="text-muted-foreground">
                <span className="font-semibold text-foreground">Email:</span> admin@salesmanagement.com
              </p>
              <p className="text-muted-foreground">
                <span className="font-semibold text-foreground">Senha:</span> password
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
