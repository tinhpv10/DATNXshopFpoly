<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppProductStock extends Model
{
    use HasFactory;

    protected $table = 'product_stocks';
    protected $fillable = [
        'sku',
        'import_price',
        'retail_price',
        'wholesale_price',
        'qty_inventory',
        'product_id',
        'media'
    ];

    public function productAttribute(): HasMany
    {
        return $this->HasMany(ProductAttribute::class);
    }

    public function Product(): BelongsTo
    {
        return $this->BelongsTo(Product::class);
    }
    public function productMedia(): BelongsTo
    {
        return $this->BelongsTo(AppProductMedia::class);
    }
}
