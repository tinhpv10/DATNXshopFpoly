<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppProductVariationValue extends Model
{
    protected $table = 'product_variation_values';
    protected $fillable = [
        'product_variation_id',
        'variation_value_name'
    ];



    public function productVariation(): BelongsTo
    {
        return $this->BelongsTo(ProductVariation::class);
    }

    public function productAttribute(): HasMany
    {
        return $this->HasMany(ProductAttribute::class);
    }

}
