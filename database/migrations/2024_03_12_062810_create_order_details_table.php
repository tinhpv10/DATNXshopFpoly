<?php

use App\Models\Order;
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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id()->comment('Mã chi tiết đơn hàng');
            $table->foreignIdFor(Order::class)->comment('Mã đơn hàng');
            $table->foreignIdFor(Shop::class)->nullable()->comment('Mã nhà bán');
            $table->string('product_image')->nullable()->comment('Ảnh sản phẩm');
            $table->string('product_price')->nullable()->comment('Giá sản phẩm');
            $table->foreignIdFor(Product::class)->comment('Sản phẩm');
            $table->foreignIdFor(ProductStock::class)->nullable()->comment('sku');
            $table->string('product_quantity')->nullable()->comment('Số lượng sản phẩm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
