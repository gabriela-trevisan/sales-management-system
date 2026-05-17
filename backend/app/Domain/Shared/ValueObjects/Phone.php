<?php

namespace App\Domain\Shared\ValueObjects;

final readonly class Phone
{
    private function __construct(private string $value) {}

    public static function fromString(?string $phone): ?self
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (! in_array(strlen($digits), [10, 11], true)) {
            throw new \App\Domain\Shared\Exceptions\DomainArgumentException('Telefone deve ter 10 ou 11 dígitos.');
        }

        return new self($digits);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
