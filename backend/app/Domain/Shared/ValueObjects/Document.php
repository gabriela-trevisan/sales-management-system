<?php

namespace App\Domain\Shared\ValueObjects;

final readonly class Document
{
    private function __construct(private string $value) {}

    public static function fromString(string $document): self
    {
        $digits = preg_replace('/[^0-9]/', '', $document) ?? '';

        if (! in_array(strlen($digits), [11, 14], true)) {
            throw new \App\Domain\Shared\Exceptions\DomainArgumentException('Documento deve ser CPF (11 dígitos) ou CNPJ (14 dígitos).');
        }

        return new self($digits);
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
