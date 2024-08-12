<?php

use App\Models\AppProductStock;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Shop;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id()->comment('Mã giỏ hàng');
            $table->bigInteger('price')->nullable(false)->comment('Giá');
            $table->string('quantity')->nullable(false)->comment('Số lượng');
            $table->foreignIdFor(AppProductStock::class)->comment('Mã sản phẩm biến thể');
            $table->foreignIdFor(Product::class)->comment('Mã sản phẩm');
            $table->foreignIdFor(Shop::class)->comment('Mã nhà bán');
            $table->foreignIdFor(Cart::class)->comment('Mã giỏ hàng');
            $table->string('media')->nullable()->comment('Media');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
