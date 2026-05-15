import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react-swc'
import path from 'path'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  // loadEnv lê os arquivos .env e as variáveis de ambiente do processo.
  // VITE_BACKEND_PROXY_TARGET é o endereço interno do backend — usado pelo
  // servidor Node.js do Vite para proxy, não é exposto ao bundle do cliente.
  const env = loadEnv(mode, process.cwd(), '')
  const backendTarget = env.VITE_BACKEND_PROXY_TARGET ?? 'http://localhost:8000'

  return {
    plugins: [react()],
    resolve: {
      alias: {
        '@': path.resolve(__dirname, './src'),
      },
    },
    server: {
      host: '0.0.0.0',
      port: 5173,
      watch: {
        usePolling: true,
      },
      /**
       * Proxy: faz todos os requests de API e CSRF saírem do mesmo origin
       * (localhost:5173) do ponto de vista do browser.
       *
       * Por que isso resolve o 419?
       * - Sem proxy: browser chama localhost:5173 → localhost:8000 (cross-origin)
       *   O cookie XSRF-TOKEN setado pelo backend pode não ser legível pelo
       *   JS em localhost:5173 dependendo do browser/SameSite policy.
       * - Com proxy: browser chama localhost:5173/api/* → Vite repassa para
       *   backend internamente. Browser vê tudo como same-origin. CSRF funciona.
       *
       * Referência: https://laravel.com/docs/11.x/sanctum#cors-and-cookies
       */
      proxy: {
        '/api': {
          target: backendTarget,
          changeOrigin: true,
        },
        '/sanctum': {
          target: backendTarget,
          changeOrigin: true,
        },
      },
    },
  }
})
