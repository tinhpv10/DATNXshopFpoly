<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
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
            $table->string('product_name')->nullable(false)->comment('Tên sản phẩm');
            $table->string('product_image')->nullable(false)->comment('Ảnh sản phẩm');
            $table->string('product_price')->nullable(false)->comment('Giá sản phẩm');
            $table->foreignIdFor(Product::class)->comment('Mã sản phẩm');
            $table->foreignIdFor(Shop::class)->nullable(false)->comment('Mã nhà bán');
            $table->string('product_quantity')->nullable(false)->comment('Số lượng sản phẩm');
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
