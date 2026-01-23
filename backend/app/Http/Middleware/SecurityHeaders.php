<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 * 
 * Adiciona headers de segurança HTTP para proteger contra:
 * - Clickjacking (X-Frame-Options)
 * - MIME sniffing (X-Content-Type-Options)
 * - XSS (X-XSS-Protection - legacy, Content-Security-Policy - moderno)
 * - Information leakage (Referrer-Policy)
 * - Feature abuse (Permissions-Policy)
 * - Man-in-the-middle (HSTS - apenas produção)
 * - Data injection (Content-Security-Policy)
 * 
 * Conformidade: OWASP A05:2021 (Security Misconfiguration)
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // X-Frame-Options: Previne clickjacking
        // DENY: Não permite que a página seja exibida em frame/iframe
        $response->headers->set('X-Frame-Options', 'DENY');

        // X-Content-Type-Options: Previne MIME sniffing
        // nosniff: Navegador não tenta adivinhar o Content-Type
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection: Proteção XSS legacy (navegadores antigos)
        // 1; mode=block: Ativa filtro XSS e bloqueia página se detectar ataque
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy: Controla informações de referência
        // strict-origin-when-cross-origin: Envia apenas origem em requisições cross-origin via HTTPS
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy: Controla acesso a APIs do navegador
        // Desabilita APIs não utilizadas pelo sistema
        $response->headers->set('Permissions-Policy', 
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=()'
        );

        // Content-Security-Policy: Previne XSS, clickjacking e data injection
        // Diretivas configuradas por ambiente (strict em produção, permissivo em desenvolvimento)
        $response->headers->set('Content-Security-Policy', $this->buildCspPolicy());

        // HSTS: Force HTTPS (apenas em produção)
        // max-age=31536000: Válido por 1 ano
        // includeSubDomains: Aplica a todos os subdomínios
        // preload: Pode ser incluído na lista HSTS do navegador
        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        return $response;
    }

    /**
     * Constrói a política Content-Security-Policy baseada no ambiente.
     * 
     * Produção: Política restritiva (apenas recursos de origins confiáveis)
     * Desenvolvimento: Política permissiva (Vite HMR, inline styles do Tailwind)
     * 
     * @return string CSP policy string
     */
    private function buildCspPolicy(): string
    {
        $isProduction = app()->environment('production');
        
        // Frontend URL (React/Vite)
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $parsedUrl = parse_url($frontendUrl);
        $frontendOrigin = ($parsedUrl['scheme'] ?? 'http') . '://' . ($parsedUrl['host'] ?? 'localhost') . 
                         (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '');

        if ($isProduction) {
            // Produção: Política strict
            return implode('; ', [
                "default-src 'self'",
                "script-src 'self'",
                "style-src 'self'",
                "img-src 'self' data: https:",
                "font-src 'self' data:",
                "connect-src 'self' {$frontendOrigin}",
                "frame-ancestors 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "upgrade-insecure-requests"
            ]);
        }

        // Desenvolvimento: Política permissiva (Vite HMR + Tailwind inline styles)
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' {$frontendOrigin}",
            "style-src 'self' 'unsafe-inline' {$frontendOrigin}",
            "img-src 'self' data: https: http:",
            "font-src 'self' data: {$frontendOrigin}",
            "connect-src 'self' {$frontendOrigin} ws: wss:",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ]);
    }
}

