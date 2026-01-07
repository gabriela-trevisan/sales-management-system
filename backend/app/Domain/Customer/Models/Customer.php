<?php

namespace App\Domain\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

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
     * Get the user assigned to this customer
     */
    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    /**
     * Get the addresses for the customer
     */
    public function addresses()
    {
        return $this->hasMany(\App\Domain\Customer\Models\CustomerAddress::class);
    }

    /**
     * Get the contacts for the customer
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
