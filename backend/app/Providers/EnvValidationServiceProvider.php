<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RuntimeException;

/**
 * Environment Variables Validation Service Provider
 * 
 * Valida variáveis críticas do .env no boot da aplicação.
 * Fail-fast: Lança exceção se variáveis obrigatórias estiverem ausentes ou inválidas.
 * 
 * Benefícios:
 * - Detecta problemas de configuração antes de atingir produção
 * - Mensagens de erro claras para desenvolvedores
 * - Previne comportamentos inesperados por .env incompleto
 * - Documentação implícita de variáveis obrigatórias
 */
class EnvValidationServiceProvider extends ServiceProvider
{
    /**
     * Variáveis obrigatórias do .env
     * 
     * @var array<string, string> Key = variável, Value = descrição
     */
    private const REQUIRED_VARS = [
        'APP_NAME' => 'Nome da aplicação',
        'APP_ENV' => 'Ambiente (local, staging, production)',
        'APP_KEY' => 'Chave de criptografia (gerada por php artisan key:generate)',
        'APP_URL' => 'URL base da aplicação backend',
        
        'DB_CONNECTION' => 'Driver de banco de dados (mysql, pgsql, sqlite)',
        'DB_HOST' => 'Host do banco de dados',
        'DB_PORT' => 'Porta do banco de dados',
        'DB_DATABASE' => 'Nome do banco de dados',
        'DB_USERNAME' => 'Usuário do banco de dados',
        'DB_PASSWORD' => 'Senha do banco de dados',
        
        'REDIS_HOST' => 'Host do Redis',
        'REDIS_PORT' => 'Porta do Redis',
    ];

    /**
     * Variáveis com validação de formato
     * 
     * @var array<string, array{pattern: string, message: string}>
     */
    private const FORMAT_VALIDATIONS = [
        'APP_ENV' => [
            'pattern' => '/^(local|staging|production)$/',
            'message' => 'APP_ENV deve ser: local, staging ou production',
        ],
        'APP_URL' => [
            'pattern' => '/^https?:\/\/.+/',
            'message' => 'APP_URL deve começar com http:// ou https://',
        ],
        'DB_CONNECTION' => [
            'pattern' => '/^(mysql|pgsql|sqlite|sqlsrv)$/',
            'message' => 'DB_CONNECTION deve ser: mysql, pgsql, sqlite ou sqlsrv',
        ],
        'DB_PORT' => [
            'pattern' => '/^\d+$/',
            'message' => 'DB_PORT deve ser um número',
        ],
        'REDIS_PORT' => [
            'pattern' => '/^\d+$/',
            'message' => 'REDIS_PORT deve ser um número',
        ],
    ];

    /**
     * Bootstrap do service provider.
     * 
     * Executa validações no boot para fail-fast.
     */
    public function boot(): void
    {
        // Skip validation em comandos específicos (key:generate, migrate, etc)
        if ($this->shouldSkipValidation()) {
            return;
        }

        $this->validateRequiredVariables();
        $this->validateFormats();
        $this->validateAppKey();
    }

    /**
     * Verifica se deve pular validação (comandos de setup ou config cacheada).
     */
    private function shouldSkipValidation(): bool
    {
        // Quando a config está em cache, o Laravel não carrega o .env —
        // env() retorna null para todas as variáveis. A validação já ocorreu
        // durante a geração do cache, portanto é seguro pular aqui.
        if (app()->configurationIsCached()) {
            return true;
        }

        if (!app()->runningInConsole()) {
            return false;
        }

        $command = $_SERVER['argv'][1] ?? '';
        
        // Comandos que podem rodar sem .env completo
        $allowedCommands = [
            'key:generate',
            'env:decrypt',
            'env:encrypt',
        ];

        foreach ($allowedCommands as $allowed) {
            if (str_contains($command, $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Valida se todas as variáveis obrigatórias estão definidas.
     * 
     * @throws RuntimeException Se variável obrigatória estiver ausente
     */
    private function validateRequiredVariables(): void
    {
        $missing = [];

        foreach (self::REQUIRED_VARS as $var => $description) {
            $value = env($var);
            
            if ($value === null || $value === '') {
                $missing[] = "{$var} ({$description})";
            }
        }

        if (!empty($missing)) {
            $this->throwValidationError(
                'Variáveis obrigatórias ausentes no .env',
                $missing
            );
        }
    }

    /**
     * Valida formato das variáveis.
     * 
     * @throws RuntimeException Se formato for inválido
     */
    private function validateFormats(): void
    {
        $invalid = [];

        foreach (self::FORMAT_VALIDATIONS as $var => $validation) {
            $value = env($var);
            
            if ($value && !preg_match($validation['pattern'], $value)) {
                $invalid[] = "{$var}: {$validation['message']} (atual: {$value})";
            }
        }

        if (!empty($invalid)) {
            $this->throwValidationError(
                'Variáveis com formato inválido no .env',
                $invalid
            );
        }
    }

    /**
     * Valida APP_KEY (32 caracteres base64).
     * 
     * @throws RuntimeException Se APP_KEY for inválida
     */
    private function validateAppKey(): void
    {
        $appKey = env('APP_KEY');
        
        if (!$appKey) {
            return; // Já validado em validateRequiredVariables
        }

        // APP_KEY deve ser base64:XYZ... com 44 caracteres após base64:
        if (!preg_match('/^base64:.{44,}$/', $appKey)) {
            throw new RuntimeException(
                "APP_KEY inválida. Execute: php artisan key:generate\n" .
                "Formato esperado: base64:XYZ... (32 bytes em base64)"
            );
        }
    }

    /**
     * Lança exceção de validação com mensagens formatadas.
     * 
     * @param string $title Título do erro
     * @param array<string> $errors Lista de erros
     * @throws RuntimeException
     */
    private function throwValidationError(string $title, array $errors): void
    {
        $message = "\n";
        $message .= "╔═══════════════════════════════════════════════════════════════╗\n";
        $message .= "║  ❌ ERRO DE CONFIGURAÇÃO: .env INVÁLIDO                      ║\n";
        $message .= "╚═══════════════════════════════════════════════════════════════╝\n\n";
        $message .= "{$title}:\n\n";
        
        foreach ($errors as $error) {
            $message .= "  • {$error}\n";
        }
        
        $message .= "\n";
        $message .= "📝 Ações recomendadas:\n";
        $message .= "  1. Copie .env.example: cp .env.example .env\n";
        $message .= "  2. Configure as variáveis obrigatórias\n";
        $message .= "  3. Gere APP_KEY: php artisan key:generate\n";
        $message .= "\n";

        throw new RuntimeException($message);
    }
}
