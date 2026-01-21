<?php

namespace App\Domain\Customer\Models;

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
    ];

    /**
     * Remove formatação do documento antes de salvar.
     * 
     * Armazena apenas números (CPF: 11 dígitos, CNPJ: 14 dígitos).
     */
    public function setDocumentAttribute($value): void
    {
        $this->attributes['document'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Normaliza email antes de salvar.
     * 
     * Converte para minúsculas e remove espaços.
     */
    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    /**
     * Remove formatação do telefone antes de salvar.
     * 
     * Armazena apenas números (fixo: 10 dígitos, celular: 11 dígitos).
     */
    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = $value ? preg_replace('/[^0-9]/', '', $value) : null;
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
     * Get the opportunities for the customer
     */
    public function opportunities()
    {
        return $this->hasMany(\App\Domain\Sales\Models\Opportunity::class);
    }
}
