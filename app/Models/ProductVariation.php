<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class ProductVariation extends Model
{
    use HasFactory;

    protected $table = 'product_variations';
    protected $fillable = [
        'variation_name',
        'product_id'
    ];
    public function Product(): BelongsTo
    {
        return $this->BelongsTo(Product::class);
    }
}
