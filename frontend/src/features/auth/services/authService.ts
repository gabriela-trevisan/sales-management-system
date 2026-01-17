import api from '@/services/api';
import { LoginCredentials, AuthResponse, User } from '@/types/auth';

/**
 * Serviço de autenticação.
 */
export const authService = {
  /**
   * Realiza login no sistema.
   */
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await api.post('/auth/login', credentials);
    return response.data;
  },

  /**
   * Realiza logout do usuário.
   */
  async logout(): Promise<void> {
    await api.post('/auth/logout');
  },

  /**
   * Busca dados do usuário autenticado.
   */
  async me(): Promise<User> {
    const response = await api.get('/auth/me');
    return response.data;
  },
};
