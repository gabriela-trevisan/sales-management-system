import api, { initCsrf } from '@/services/api';
import type { LoginCredentials, AuthResponse, User } from '@/types/auth';

/**
 * Serviço de autenticação.
 *
 * Usa Sanctum SPA cookie authentication:
 * - O login obtém o CSRF cookie antes de enviar credenciais
 * - O token de sessão trafega exclusivamente via cookie httpOnly
 * - Nenhum token é armazenado em localStorage ou sessionStorage
 */
export const authService = {
  /**
   * Realiza login no sistema.
   * 1. Obtém o CSRF cookie do Sanctum (XSRF-TOKEN)
   * 2. Envia as credenciais — o axios inclui X-XSRF-TOKEN automaticamente
   * 3. O servidor inicia a sessão e retorna o usuário (sem token no body para o SPA)
   */
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    await initCsrf();
    const response = await api.post('/auth/login', credentials);
    return response.data;
  },

  /**
   * Realiza logout do usuário.
   * O servidor invalida a sessão e o cookie expira.
   */
  async logout(): Promise<void> {
    await api.post('/auth/logout');
  },

  /**
   * Busca dados do usuário autenticado via sessão ativa.
   * Usado para verificar se a sessão ainda é válida ao carregar a aplicação.
   */
  async me(): Promise<User> {
    const response = await api.get('/auth/me');
    return response.data.user;
  },
};
