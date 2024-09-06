<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


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
        'follower',
        'province_id',
        'district_id',
        'ward_id',
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
    public function province(): BelongsTo
    {
        return $this->BelongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->BelongsTo(District::class);
    }

    public function ward(): BelongsTo
    {
        return $this->BelongsTo(Ward::class);
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

        static::updated(function ($shop) {
            // Kiểm tra xem yêu cầu có chứa dữ liệu cập nhật địa chỉ không
            $request = Request::capture(); // Lấy dữ liệu yêu cầu hiện tại
            $updates = $request->input('components.0.updates', []);

            // Kiểm tra nếu dữ liệu cập nhật địa chỉ có trong yêu cầu
            if (Auth::check() && array_key_exists('data.address', $updates)) {
                // Ghi log thông tin yêu cầu để kiểm tra
                Log::info('Update Address Request:', [
                    'user_id' => Auth::id(),
                    'request_path' => $request->path(),
                    'request_data' => $request->all()
                ]);

                // Cập nhật địa chỉ người dùng
                $userAddress = Auth::user()->userAddress()->firstOrNew([
                    'user_id' => Auth::id(),
                ]);

                // Cập nhật địa chỉ shop
                $userAddress->name = $shop->name;
                $userAddress->phone = $shop->phone;
                $userAddress->province_id = $shop->province->id;
                $userAddress->district_id = $shop->district->id;
                $userAddress->ward_id = $shop->ward->id;
                $userAddress->address_specific = $shop->address;
                $userAddress->save();
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getFullAddress()
    {
        $province = $this->province ? Str::ascii($this->province->name) : '';
        $district = $this->district ? Str::ascii($this->district->name) : '';
        $ward = $this->ward ? Str::ascii($this->ward->name) : '';

        return trim("{$ward}, {$district}, {$province}");
    }

}
