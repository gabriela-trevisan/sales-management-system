<?php

namespace App\Domain\Customer\Models;

use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Phone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerContact extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'role',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Normaliza email antes de salvar.
     * 
     * Converte para minúsculas e remove espaços.
     */
    public function setEmailAttribute(?string $value): void
    {
        $this->attributes['email'] = $value !== null && $value !== ''
            ? Email::fromString($value)->value()
            : null;
    }

    public function setPhoneAttribute(?string $value): void
    {
        $phone = Phone::fromString($value);
        $this->attributes['phone'] = $phone?->value();
    }

    /**
     * Cliente proprietário do contato.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
