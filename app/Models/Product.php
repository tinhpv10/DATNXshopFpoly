<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'supplier_id',
        'category_id',
        'shop_id',
        'brand_id',
        'name',
        'slug',
        'regular_price',
        'sale_price',
        'sku',
        'rating',
        'view_count',
        'sold_count',
        'description',
        'origin',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'pause'
    ];
    protected $casts = [
        'meta_keyword' => 'array',
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

    public function Brand(): BelongsTo
    {
        return $this->BelongsTo(Brand::class);
    }

    public function review(): HasMany
    {
        return $this->HasMany(Review::class);
    }

    public function like(): HasMany
    {
        return $this->HasMany(Like::class);
    }

    public function productMedia(): HasMany
    {
        return $this->hasMany(AppProductMedia::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }


    protected static function booted()
    {
        static::created(function ($product) {
            $user = Auth::user();

            if ($user) {
                // Thiết lập shop_id cho sản phẩm
                $product->shop_id = $user->shop_id;
                $product->save();

                // Thiết lập shop_id cho nhà cung cấp nếu có
                if ($product->supplier) {
                    $product->supplier->shop_id = $product->shop_id;
                    $product->supplier->save();
                }
                // Hàm để thêm danh mục hiện tại vào shop nếu chưa tồn tại
                self::addCategoryToShop($product->category_id, $user->shop_id);

                // Lấy danh mục cấp cha đầu tiên của danh mục sản phẩm
                $productCategory = $product->category;
                $rootParentCategory = $productCategory->getRootParent();
                if ($rootParentCategory->name === 'Thực Phẩm & Đồ Uống' || $rootParentCategory->name === 'Sức Khỏe & Sắc Đẹp') {
                    $product->pause = 1;
                    $product->save();
                    if ($product->pause == 1) {
                        // Gửi thông báo tới các quản trị viên super_admin
                        $superAdmins = User::role('super_admin')->get();
                        foreach ($superAdmins as $admin) {
                            Notification::make()
                                ->title('Có một sản phẩm thuộc danh mục chờ duyệt ,Danh mục: ' . $rootParentCategory->name)
                                ->icon('heroicon-o-squares-2x2')
                                ->body('Sản phẩm Chờ bạn vào xét duyệt : ' . $product->name)
                                ->sendToDatabase($admin);
                        }
                    }
                }
            }
        });
    }


    // Hàm để thêm danh mục hiện tại vào shop nếu chưa tồn tại
    protected static function addCategoryToShop($categoryId, $shopId)
    {
        $category = Category::find($categoryId);

        if ($category) {
            // Kiểm tra nếu danh mục hiện tại đã tồn tại trong shop
            $existingCategoryShop = CategoryShop::where('category_id', $category->id)
                ->where('shop_id', $shopId)
                ->first();

            if (!$existingCategoryShop) {
                CategoryShop::create([
                    'category_id' => $category->id,
                    'shop_id' => $shopId,
                ]);
            }
        }
    }


    public function productVariation(): HasMany
    {
        return $this->HasMany(AppProductVariation::class);
    }

    public function productStock(): HasMany
    {
        return $this->HasMany(AppProductStock::class);
    }

    // Hàm đêm số lượng chờ duyệt
    public static function countPendingApproval()
    {
        return self::where('pause', 1)->count();
    }
    // lấy ảnh chính
    public function mainMedia(){
        return $this->hasOne(AppProductMedia::class)->where('is_main', 1);
    }
    // hàm lấy giá
    public function getPrice(){
        return $this->sale_price ?? $this->regular_price;
    }

}
