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
        return $this->BelongsTo(AppProductStock::class);
    }

    public function appProductVariationValue(): BelongsTo
    {
        return $this->BelongsTo(AppProductVariationValue::class);
    }

}
