<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'shop_id',
        'product_image',
        'product_price',
        'product_id',
        'product_quantity',
        'app_product_stock_id',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function Shop(): BelongsTo
    {
        return $this->BelongsTo(Shop::class);
    }

    public function Product(): BelongsTo
    {
        return $this->BelongsTo(Product::class);
    }
    public function appProductStock()
    {
        return $this->belongsTo(AppProductStock::class, 'app_product_stock_id');
    }

    public function productVariations()
    {
        return $this->appProductStock ? $this->appProductStock->productAttribute->appProductVariation : collect();
    }
}
