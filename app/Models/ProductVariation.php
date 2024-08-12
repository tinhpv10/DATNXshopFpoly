<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;


class ProductVariation extends Model
{
    use HasFactory;

    protected $table = 'product_variations';
    protected $fillable = [
        'variation_name',
        'product_id',
        'shop_id'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function appProductVariationValue(): HasMany
    {
        return $this->hasMany(AppProductVariationValue::class);
    }
    protected static function booted()
    {
        static::created(function ($ProductVariation) {
            $user = Auth::user();
            if ($user) {
                $ProductVariation->shop_id = $user->shop_id;
                $ProductVariation->save();
            }
        });
    }

}
