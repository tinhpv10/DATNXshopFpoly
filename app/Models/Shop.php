<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Shop extends Model
{
    protected $table = 'shops';

    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'avatar',
        'email',
        'phone',
        'address',
        'description',
        'rating',
        'status',
        'follower'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function Category(): HasMany
    {
        return $this->HasMany(Category::class);
    }

    public function shopInfo(): HasOne
    {
        return $this->hasOne(ShopInfo::class);
    }

    public function categoryShop()
    {
        return $this->belongsToMany(Category::class, 'category_shops', 'shop_id', 'category_id');
    }
    public static function boot()
    {
        parent::boot(); // Call parent's boot method first

        static::creating(function ($shop) {

            $shop->user_id = Auth::id();
        });
    }
    protected static function booted()
    {
        static::created(function ($shop) {
            // Find the user and update their shop_id
            $user = User::find($shop->user_id);
            if ($user) {
                $user->shop_id = $shop->id;
                $user->save();
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
