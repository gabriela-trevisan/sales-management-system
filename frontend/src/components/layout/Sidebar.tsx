import { Link, useLocation } from 'react-router-dom';

const menuItems = [
  { path: '/dashboard', label: 'Dashboard', icon: '📊' },
  { path: '/customers', label: 'Clientes', icon: '👥' },
  { path: '/opportunities', label: 'Pipeline', icon: '🎯' },
  { path: '/products', label: 'Produtos', icon: '📦' },
  { path: '/proposals', label: 'Propostas', icon: '📄' },
  { path: '/commissions', label: 'Comissões', icon: '💰' },
  { path: '/analytics', label: 'Analytics', icon: '📈' },
];

export function Sidebar() {
  const location = useLocation();

  return (
    <aside className="w-64 bg-gray-900 text-white min-h-screen">
      <div className="p-4">
        <h1 className="text-xl font-bold">Sales Management</h1>
      </div>
      
      <nav className="mt-6">
        {menuItems.map((item) => (
          <Link
            key={item.path}
            to={item.path}
            className={`
              flex items-center gap-3 px-4 py-3 transition-colors
              ${location.pathname === item.path 
                ? 'bg-blue-600 text-white' 
                : 'text-gray-300 hover:bg-gray-800'
              }
            `}
          >
            <span className="text-xl">{item.icon}</span>
            <span>{item.label}</span>
          </Link>
        ))}
      </nav>
    </aside>
  );
}
