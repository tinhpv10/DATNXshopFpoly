<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'price',
        'quantity',
        'sku',
        'product_id',
        'shop_id',
        'cart_id',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productMedia(): HasOne
    {
        return $this->hasOne(ProductMedia::class, 'product_id', 'product_id')->where('is_main', 1);
    }
}
