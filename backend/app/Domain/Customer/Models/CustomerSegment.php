<?php

namespace App\Domain\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rfm_min_score',
        'rfm_max_score',
    ];

    protected $casts = [
        'rfm_min_score' => 'integer',
        'rfm_max_score' => 'integer',
    ];

    /**
     * Get the customers in this segment
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'segment_id');
    }
}
