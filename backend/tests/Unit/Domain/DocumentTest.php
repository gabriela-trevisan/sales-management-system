<?php

namespace Tests\Unit\Domain;

use App\Domain\Shared\Exceptions\DomainArgumentException;
use App\Domain\Shared\ValueObjects\Document;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    // -------------------------------------------------------------------------
    // CPF
    // -------------------------------------------------------------------------

    public function test_normalizes_valid_cpf_removing_formatting(): void
    {
        $document = Document::fromString('529.982.247-25');

        $this->assertSame('52998224725', $document->value());
        $this->assertTrue($document->isCpf());
        $this->assertFalse($document->isCnpj());
    }

    public function test_accepts_valid_cpf_without_formatting(): void
    {
        $document = Document::fromString('52998224725');

        $this->assertSame('52998224725', $document->value());
    }

    public function test_rejects_cpf_with_invalid_check_digits(): void
    {
        $this->expectException(DomainArgumentException::class);
        $this->expectExceptionMessageMatches('/CPF inválido/u');

        // Último dígito alterado: deveria terminar em 25, não 26
        Document::fromString('529.982.247-26');
    }

    public function test_rejects_cpf_with_all_same_digits(): void
    {
        $this->expectException(DomainArgumentException::class);

        Document::fromString('111.111.111-11');
    }

    // -------------------------------------------------------------------------
    // CNPJ
    // -------------------------------------------------------------------------

    public function test_normalizes_valid_cnpj_removing_formatting(): void
    {
        $document = Document::fromString('11.222.333/0001-81');

        $this->assertSame('11222333000181', $document->value());
        $this->assertTrue($document->isCnpj());
        $this->assertFalse($document->isCpf());
    }

    public function test_accepts_valid_cnpj_without_formatting(): void
    {
        $document = Document::fromString('11222333000181');

        $this->assertSame('11222333000181', $document->value());
    }

    public function test_rejects_cnpj_with_invalid_check_digits(): void
    {
        $this->expectException(DomainArgumentException::class);
        $this->expectExceptionMessageMatches('/CNPJ inválido/u');

        // Último dígito alterado: deveria terminar em 81, não 82
        Document::fromString('11.222.333/0001-82');
    }

    public function test_rejects_cnpj_with_all_same_digits(): void
    {
        $this->expectException(DomainArgumentException::class);

        Document::fromString('00.000.000/0000-00');
    }

    // -------------------------------------------------------------------------
    // Comprimento inválido
    // -------------------------------------------------------------------------

    public function test_rejects_document_with_invalid_length(): void
    {
        $this->expectException(DomainArgumentException::class);
        $this->expectExceptionMessageMatches('/CPF.*CNPJ/u');

        Document::fromString('123');
    }

    // -------------------------------------------------------------------------
    // equals
    // -------------------------------------------------------------------------

    public function test_two_documents_with_same_value_are_equal(): void
    {
        $a = Document::fromString('529.982.247-25');
        $b = Document::fromString('52998224725');

        $this->assertTrue($a->equals($b));
    }

    public function test_two_documents_with_different_values_are_not_equal(): void
    {
        $a = Document::fromString('529.982.247-25');
        $b = Document::fromString('11.222.333/0001-81');

        $this->assertFalse($a->equals($b));
    }
}
