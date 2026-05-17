<?php

namespace App\Domain\Shared\ValueObjects;

final readonly class Email
{
    private function __construct(private string $value) {}

    public static function fromString(string $email): self
    {
        $normalized = strtolower(trim($email));

        if ($normalized === '' || ! filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new \App\Domain\Shared\Exceptions\DomainArgumentException('E-mail inválido.');
        }

        return new self($normalized);
    }

    public function value(): string
    {
        return $this->value;
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
