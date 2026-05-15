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

        $response->headers->set('X-Frame-Options', 'DENY');

        $response->headers->set('X-Content-Type-Options', 'nosniff');

        $response->headers->set('X-XSS-Protection', '1; mode=block');

        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        $response->headers->set('Permissions-Policy', 
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=()'
        );

        $response->headers->set('Content-Security-Policy', $this->buildCspPolicy());

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
        
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $parsedUrl = parse_url($frontendUrl);
        $frontendOrigin = ($parsedUrl['scheme'] ?? 'http') . '://' . ($parsedUrl['host'] ?? 'localhost') . 
                         (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '');

        if ($isProduction) {
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

        // política permissiva em dev: Tailwind usa inline styles e Vite HMR requer unsafe-eval
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

