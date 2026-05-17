<?php

namespace App\Domain\Shared\ValueObjects;

final readonly class Document
{
    private function __construct(private string $value) {}

    public static function fromString(string $document): self
    {
        $digits = preg_replace('/[^0-9]/', '', $document) ?? '';

        if (strlen($digits) === 11) {
            if (! self::isValidCpf($digits)) {
                throw new \App\Domain\Shared\Exceptions\DomainArgumentException('CPF inválido. Verifique os dígitos verificadores.');
            }

            return new self($digits);
        }

        if (strlen($digits) === 14) {
            if (! self::isValidCnpj($digits)) {
                throw new \App\Domain\Shared\Exceptions\DomainArgumentException('CNPJ inválido. Verifique os dígitos verificadores.');
            }

            return new self($digits);
        }

        throw new \App\Domain\Shared\Exceptions\DomainArgumentException('Documento deve ser CPF (11 dígitos) ou CNPJ (14 dígitos).');
    }

    /**
     * Valida CPF usando o algoritmo oficial da Receita Federal (módulo 11).
     *
     * Rejeita sequências homogêneas (ex: 111.111.111-11) e verifica
     * ambos os dígitos verificadores.
     */
    private static function isValidCpf(string $digits): bool
    {
        if (preg_match('/^(\d)\1{10}$/', $digits)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $digits[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        if ($digit1 !== (int) $digits[9]) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $digits[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        return $digit2 === (int) $digits[10];
    }

    /**
     * Valida CNPJ usando o algoritmo oficial da Receita Federal (módulo 11).
     *
     * Rejeita sequências homogêneas (ex: 00.000.000/0000-00) e verifica
     * ambos os dígitos verificadores.
     */
    private static function isValidCnpj(string $digits): bool
    {
        if (preg_match('/^(\d)\1{13}$/', $digits)) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $digits[$i] * $weights1[$i];
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        if ($digit1 !== (int) $digits[12]) {
            return false;
        }

        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $digits[$i] * $weights2[$i];
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        return $digit2 === (int) $digits[13];
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isCpf(): bool
    {
        return strlen($this->value) === 11;
    }

    public function isCnpj(): bool
    {
        return strlen($this->value) === 14;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
