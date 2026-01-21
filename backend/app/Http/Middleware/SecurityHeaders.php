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
 * - XSS (X-XSS-Protection - legacy)
 * - Information leakage (Referrer-Policy)
 * - Feature abuse (Permissions-Policy)
 * - Man-in-the-middle (HSTS - apenas produção)
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
}

