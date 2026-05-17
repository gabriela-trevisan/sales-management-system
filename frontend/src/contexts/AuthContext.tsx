import { createContext, useContext, useState, useEffect } from 'react';
import type { ReactNode } from 'react';
import { authService } from '@/features/auth/services/authService';
import type { User, LoginCredentials, AuthContextType } from '@/types/auth';

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  /**
   * Inicializa do cache local imediatamente para evitar flash de conteúdo.
   * O useEffect abaixo valida a sessão com o servidor de forma assíncrona.
   * Usar lazy initializer evita chamar setState sincronamente dentro de um effect.
   */
  const [user, setUser] = useState<User | null>(() => {
    const cachedUser = localStorage.getItem('user');
    if (!cachedUser) return null;
    try {
      return JSON.parse(cachedUser) as User;
    } catch {
      localStorage.removeItem('user');
      return null;
    }
  });
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    /**
     * Verifica com o servidor se a sessão ainda é válida.
     * Com cookie httpOnly (Sanctum SPA), a sessão é a fonte de verdade.
     */
    authService
      .me()
      .then((serverUser) => {
        setUser(serverUser);
        localStorage.setItem('user', JSON.stringify(serverUser));
      })
      .catch(() => {
        // sessão expirada ou inválida
        setUser(null);
        localStorage.removeItem('user');
      })
      .finally(() => setIsLoading(false));
  }, []);

  const login = async (credentials: LoginCredentials) => {
    const response = await authService.login(credentials);
    setUser(response.user);
    localStorage.setItem('user', JSON.stringify(response.user));
  };

  const logout = () => {
    authService.logout().catch(() => {
      // continua o logout local mesmo se o backend falhar
    });
    setUser(null);
    localStorage.removeItem('user');
  };

  const value: AuthContextType = {
    user,
    login,
    logout,
    isAuthenticated: !!user,
    isLoading,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

// eslint-disable-next-line react-refresh/only-export-components
export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
