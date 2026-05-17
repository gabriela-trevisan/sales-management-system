<?php

namespace App\Domain\Customer\Models;

use App\Domain\Customer\Enums\CustomerStatus;
use App\Domain\Shared\Exceptions\InvalidDomainStateException;
use App\Domain\Shared\ValueObjects\Document;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Phone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /**
     * Atributos auditados (LGPD compliance).
     * 
     * Registra mudanças em dados pessoais conforme Art. 46 LGPD.
     *
     * @var array<int, string>
     */
    protected $auditInclude = [
        'name',
        'document',
        'email',
        'phone',
        'segment_id',
        'status',
        'assigned_to',
    ];

    /**
     * Apenas campos modificados são auditados (performance).
     *
     * @var bool
     */
    protected $auditTimestamps = false;

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'segment_id',
        'rfm_score',
        'status',
        'assigned_to',
    ];

    protected $casts = [
        'rfm_score' => 'array',
        'assigned_to' => 'integer',
    ];

    /**
     * Remove formatação do documento antes de salvar.
     * 
     * Armazena apenas números (CPF: 11 dígitos, CNPJ: 14 dígitos).
     */
    public function setDocumentAttribute(string $value): void
    {
        $this->attributes['document'] = Document::fromString($value)->value();
    }

    public function setEmailAttribute(string $value): void
    {
        $this->attributes['email'] = Email::fromString($value)->value();
    }

    public function setPhoneAttribute(?string $value): void
    {
        $phone = Phone::fromString($value);
        $this->attributes['phone'] = $phone?->value();
    }

    public function documentValue(): Document
    {
        return Document::fromString($this->document);
    }

    public function emailValue(): Email
    {
        return Email::fromString($this->email);
    }

    public function phoneValue(): ?Phone
    {
        return Phone::fromString($this->phone);
    }

    /**
     * Atualiza dados de perfil com validação via Value Objects.
     */
    public function updateProfile(
        ?string $name = null,
        ?Document $document = null,
        ?Email $email = null,
    ): void {
        if ($name !== null) {
            $this->name = $name;
        }

        if ($document !== null) {
            $this->document = $document->value();
        }

        if ($email !== null) {
            $this->email = $email->value();
        }
    }

    public function updatePhone(?Phone $phone): void
    {
        $this->phone = $phone?->value();
    }

    public function assignSegment(?int $segmentId): void
    {
        $this->segment_id = $segmentId;
    }

    public function applyStatus(CustomerStatus $status): void
    {
        match ($status) {
            CustomerStatus::Active => $this->activate(),
            CustomerStatus::Inactive => $this->deactivate(),
            CustomerStatus::Prospect => $this->markAsProspect(),
            CustomerStatus::Churned => $this->markAsChurned(),
        };
    }

    public function customerStatus(): CustomerStatus
    {
        return CustomerStatus::from($this->status);
    }

    public function activate(): void
    {
        $this->status = CustomerStatus::Active->value;
    }

    public function deactivate(): void
    {
        $this->status = CustomerStatus::Inactive->value;
    }

    public function markAsProspect(): void
    {
        $this->status = CustomerStatus::Prospect->value;
    }

    public function markAsChurned(): void
    {
        if ($this->customerStatus() === CustomerStatus::Churned) {
            return;
        }

        $this->status = CustomerStatus::Churned->value;
    }

    public function assignTo(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidDomainStateException('O responsável pelo cliente deve ser um usuário válido.');
        }

        $this->assigned_to = $userId;
    }

    /**
     * Usuário responsável pelo cliente.
     */
    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    /**
     * Segmento do cliente.
     */
    public function segment()
    {
        return $this->belongsTo(\App\Domain\Customer\Models\CustomerSegment::class, 'segment_id');
    }

    /**
     * Endereços do cliente.
     */
    public function addresses()
    {
        return $this->hasMany(\App\Domain\Customer\Models\CustomerAddress::class);
    }

    /**
     * Contatos do cliente.
     */
    public function contacts()
    {
        return $this->hasMany(\App\Domain\Customer\Models\CustomerContact::class);
    }

    /**
     * Get the opportunities for the customer.
     * 
     * TODO: Implementar quando o módulo Opportunities (Module 4) for criado.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function opportunities()
    // {
    //     return $this->hasMany(\App\Domain\Sales\Models\Opportunity::class);
    // }
}
