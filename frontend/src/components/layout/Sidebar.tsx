import { Link, useLocation } from 'react-router-dom';
import { 
  LayoutDashboard, 
  Users, 
  Target, 
  Package, 
  FileText, 
  DollarSign, 
  TrendingUp,
  Moon,
  Sun
} from 'lucide-react';
import { useTheme } from '@/contexts/ThemeContext';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

/**
 * Sidebar navigation component with theme toggle
 * Implements modern glassmorphism design with smooth transitions
 * WCAG 2.1 AA compliant with keyboard navigation support
 */
const menuItems = [
  { path: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { path: '/customers', label: 'Clientes', icon: Users },
  { path: '/opportunities', label: 'Pipeline', icon: Target },
  { path: '/products', label: 'Produtos', icon: Package },
  { path: '/proposals', label: 'Propostas', icon: FileText },
  { path: '/commissions', label: 'Comissões', icon: DollarSign },
  { path: '/analytics', label: 'Analytics', icon: TrendingUp },
];

export function Sidebar() {
  const location = useLocation();
  const { theme, setTheme } = useTheme();

  const toggleTheme = () => {
    setTheme(theme === 'dark' ? 'light' : 'dark');
  };

  return (
    <aside className="w-64 bg-sidebar text-sidebar-foreground min-h-screen flex flex-col border-r border-sidebar-border shadow-sm">
      {/* Logo */}
      <div className="p-6 border-b border-sidebar-border">
        <div className="flex items-center gap-3">
          <div className="bg-sidebar-primary/10 p-2 rounded-lg ring-1 ring-sidebar-primary/20">
            <TrendingUp className="w-6 h-6 text-sidebar-primary" />
          </div>
          <div>
            <h1 className="text-lg font-bold">Sales Management</h1>
            <p className="text-xs text-muted-foreground">Sistema CRM</p>
          </div>
        </div>
      </div>
      
      {/* Navigation */}
      <nav className="flex-1 p-3 space-y-1 overflow-y-auto">
        {menuItems.map((item) => {
          const Icon = item.icon;
          const isActive = location.pathname === item.path;
          
          return (
            <Link
              key={item.path}
              to={item.path}
              className={cn(
                "flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-150 group select-none",
                isActive 
                  ? "bg-sidebar-primary/10 text-sidebar-primary ring-1 ring-sidebar-ring/20" 
                  : "text-sidebar-foreground/80 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground active:bg-sidebar-primary/15 active:scale-[0.98]"
              )}
            >
              <Icon className={cn(
                "w-5 h-5 transition-colors",
                isActive ? "text-sidebar-primary" : "group-hover:text-sidebar-primary"
              )} />
              <span className="font-medium text-sm">{item.label}</span>
            </Link>
          );
        })}
      </nav>

      {/* Theme Toggle */}
      <div className="p-4 border-t border-sidebar-border">
        <Button
          variant="ghost"
          size="sm"
          onClick={toggleTheme}
          className="w-full justify-start gap-3"
        >
          {theme === 'dark' ? (
            <>
              <Sun className="w-5 h-5" />
              <span className="text-sm">Modo Claro</span>
            </>
          ) : (
            <>
              <Moon className="w-5 h-5" />
              <span className="text-sm">Modo Escuro</span>
            </>
          )}
        </Button>
      </div>
    </aside>
  );
}
