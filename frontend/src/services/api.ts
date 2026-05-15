import axios from 'axios';

/**
 * URL base da API (ex.: http://localhost:8000/api)
 * URL base do backend, sem o sufixo /api (para a rota CSRF do Sanctum)
 */
const API_URL = import.meta.env.VITE_API_URL as string;
const BACKEND_URL = API_URL.replace(/\/api\/?$/, '');

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  /**
   * withCredentials: true é obrigatório para:
   * 1. Enviar o cookie de sessão httpOnly (laravel_session) a cada request
   * 2. Enviar o cookie XSRF-TOKEN como header X-XSRF-TOKEN automaticamente
   *    (axios lê o cookie e o repassa como header — o browser NÃO expõe o cookie JS)
   *
   * OWASP A07:2021 – Identification and Authentication Failures:
   * O token nunca toca o localStorage; a sessão é mantida via cookie httpOnly,
   * inacessível a qualquer script, mesmo em caso de XSS.
   */
  withCredentials: true,
});

/**
 * Obtém o cookie CSRF do Sanctum antes do primeiro request autenticado.
 *
 * O Sanctum define o cookie XSRF-TOKEN (legível pelo JS), que o axios
 * reenvia automaticamente como header X-XSRF-TOKEN em requisições não-idempotentes
 * (POST, PUT, PATCH, DELETE), protegendo contra CSRF.
 */
export async function initCsrf(): Promise<void> {
  await axios.get(`${BACKEND_URL}/sanctum/csrf-cookie`, {
    withCredentials: true,
  });
}

export default api;

