<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_attributes';
    protected $fillable = [
        'attribute_id',
        'variation_id',
        'app_product_stock_id',
        'media',
        'app_product_variation_value_id',
    ];

    public function productStock(): BelongsTo
    {
        return $this->belongsTo(AppProductStock::class, 'app_product_stock_id');
    }

    public function appProductVariation(): BelongsTo
    {
        return $this->belongsTo(AppProductVariation::class, 'variation_id');
    }

    public function appProductVariationValue(): BelongsTo
    {
        return $this->belongsTo(AppProductVariationValue::class, 'app_product_variation_value_id');
    }

}
