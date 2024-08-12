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
        'product_id',
        'product_stock_id',
        'shop_id',
        'media',
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
        return $this->hasOne(AppProductMedia::class, 'product_id', 'product_id')->where('is_main', 1);
    }
}
