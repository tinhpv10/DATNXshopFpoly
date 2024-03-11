<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\VoucherType;
use App\Models\ShippingAddress;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\OrderStatus;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('Mã đơn hàng');
            $table->foreignIdFor(ShippingAddress::class)->comment('Mã địa chỉ người dùng');
            $table->timestamp('delivery_date')->nullable(false)->comment('Ngày vận chuyển');
            $table->decimal('total_price', 10, 2)->nullable(false)->comment('Tổng tiền');
            $table->string('shipping_unit')->nullable(false)->comment('Đơn vị vận chuyển');
            $table->foreignIdFor(Product::class)->comment('Mã sản phẩm');
            $table->foreignIdFor(User::class)->comment('Mã người dùng');
            $table->foreignIdFor(Voucher::class)->comment('Mã mã giảm giá');
            $table->foreignIdFor(OrderStatus::class)->comment('Mã trạng thái đơn hàng');
            $table->foreignIdFor(VoucherType::class)->comment('Mã phương thức thanh toán');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
