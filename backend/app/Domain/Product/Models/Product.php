<?php

namespace App\Domain\Product\Models;

use App\Domain\Product\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category_id',
        'base_price',
        'cost_price',
        'unit',
        'is_active',
        'requires_approval',
        'specifications',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'specifications' => 'array',
    ];

    /**
     * Get the category that owns the product.
     *
     * @return BelongsTo<ProductCategory, Product>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Scope to filter active products.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Product> $query
     * @return \Illuminate\Database\Eloquent\Builder<Product>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Product> $query
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Builder<Product>
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
