<?php

namespace App\Models;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'category_id',
        'shop_id',
        'name',
        'slug',
        'regular_price',
        'sale_price',
        'sku',
        'Mã SKU',
        'rating',
        'view_count',
        'sold_count',
        'description',
        'origin',
        'meta_title',
        'meta_description',
        'meta_keyword',
    ];
    public function Supplier(): BelongsTo
    {
        return $this->BelongsTo(Supplier::class);
    }
    public function shop(): BelongsTo
    {
        return $this->BelongsTo(Shop::class);
    }
    public function Category(): BelongsTo
    {
        return $this->BelongsTo(Category::class);
    }

}
