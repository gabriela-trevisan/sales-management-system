<?php

namespace Tests\Unit\Domain;

use App\Domain\Shared\ValueObjects\Document;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function test_normalizes_cpf_digits(): void
    {
        $document = Document::fromString('123.456.789-00');

        $this->assertSame('12345678900', $document->value());
        $this->assertTrue($document->isCpf());
    }

    public function test_rejects_invalid_length(): void
    {
        $this->expectException(\App\Domain\Shared\Exceptions\DomainArgumentException::class);

        Document::fromString('123');
    }
}
