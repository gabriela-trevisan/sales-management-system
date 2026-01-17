<?php

namespace App\Domain\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'country',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Remove formatação do CEP antes de salvar.
     * 
     * Armazena apenas números (8 dígitos).
     */
    public function setZipCodeAttribute($value): void
    {
        $this->attributes['zip_code'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Cliente proprietário do endereço.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
